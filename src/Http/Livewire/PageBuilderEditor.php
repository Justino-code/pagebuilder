<?php

namespace Justino\PageBuilder\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Justino\PageBuilder\Contracts\StorageInterface;
use Justino\PageBuilder\Services\PageBuilderService;
use Justino\PageBuilder\Services\BlockManager;
use Justino\PageBuilder\Helpers\Translator;
use Justino\PageBuilder\Rules\UniquePageSlug;
use Livewire\Attributes\On;
use Justino\PageBuilder\DTOs\PageData;
use Justino\PageBuilder\Exceptions\PageValidationException;

class PageBuilderEditor extends Component
{
    use WithFileUploads;
    
    public $pageSlug = null;
    public $isNew = false;
    
    // Dados da página
    public $title = '';
    public $slug = '';
    public $published = false;
    public $content = [];
    public $customCss = '';
    public $customJs = '';
    public $headerEnabled = true;
    public $footerEnabled = true;
    public $theme = 'system';
    public $styles = [];
    public $version = '1.0.0';
    
    // UI State
    public $activeTab = 'content';
    public $selectedBlockIndex = null;
    public $showMediaLibrary = false;
    public $mediaFieldActive = null;
    public $showStyleEditor = false;
    public $pageStyles = [];
    public $showVersionHistory = false;
    public $versions = [];
    
    // Estado de carregamento e mensagens
    public $isSaving = false;
    public $saveMessage = '';
    public $saveStatus = ''; // success, error, warning
    public $isLoading = false;
    
    // Themes
    public $themeOptions = [
        'system' => 'Sistema',
        'light' => 'Claro',
        'dark' => 'Escuro'
    ];

    protected $listeners = [
        'mediaSelected',
        'stylesApplied',
        'themeChanged',
        'versionRestored'
    ];

    public function mount($pageSlug = null)
    {
        $this->pageSlug = $pageSlug;
        $this->isNew = is_null($pageSlug);
        
        if (!$this->isNew) {
            $this->loadPage();
        } else {
            // Inicializar dados padrão para nova página
            $this->initializeNewPage();
        }
    }
    
    /**
     * Carrega os dados da página
     */
    protected function loadPage(): void
    {
        $this->isLoading = true;
        
        try {
            $storage = app(StorageInterface::class);
            $pageData = $storage->loadPage($this->pageSlug);
            
            if (!$pageData) {
                session()->flash('error', Translator::trans('page_not_found'));
                $this->redirect(route('pagebuilder.pages.index'));
                return;
            }
            
            $this->title = $pageData->title;
            $this->slug = $pageData->slug;
            $this->published = $pageData->published;
            $this->content = $pageData->content;
            $this->customCss = $pageData->customCss;
            $this->customJs = $pageData->customJs;
            $this->headerEnabled = $pageData->headerEnabled;
            $this->footerEnabled = $pageData->footerEnabled;
            $this->theme = $pageData->theme;
            $this->styles = $pageData->styles;
            $this->version = $pageData->version;
            
            //dd($storage->listVersions($this->pageSlug));

            // Carregar versões
            $this->versions = array_map(function ($version) {
                return json_decode(json_encode($version), true);
            }, $storage->listVersions($this->pageSlug));
            
        } catch (\Exception $e) {
            session()->flash('error', Translator::trans('load_error') . ': ' . $e->getMessage());
            logger()->error('Erro ao carregar página', ['error' => $e->getMessage()]);
        } finally {
            $this->isLoading = false;
        }
    }
    
    /**
     * Inicializa dados para nova página
     */
    protected function initializeNewPage(): void
    {
        $this->title = 'Nova Página';
        $this->slug = '';
        $this->published = false;
        $this->content = [];
        $this->customCss = '';
        $this->customJs = '';
        $this->headerEnabled = true;
        $this->footerEnabled = true;
        $this->theme = config('pagebuilder.ui.default_theme', 'system');
        $this->styles = [];
        $this->version = '1.0.0';
    }
    
    /**
     * Salva a página (rascunho ou publicação)
     */
    public function save($publish = false)
    {
        $this->isSaving = true;
        $this->saveMessage = '';
        $this->saveStatus = '';
        
        try {
            $this->validate([
                'title' => 'required|string|max:255',
                'slug' => [
                    'required',
                    'alpha_dash',
                    'max:100',
                    new UniquePageSlug('page', $this->pageSlug)
                ],
            ]);
            
            $pageBuilderService = app(PageBuilderService::class);
            
            // Criar DTO com dados atuais
            $pageData = new PageData(
                title: $this->title,
                slug: $this->slug,
                content: $this->content,
                published: $publish,
                headerEnabled: $this->headerEnabled,
                footerEnabled: $this->footerEnabled,
                customCss: $this->customCss,
                customJs: $this->customJs,
                theme: $this->theme,
                styles: $this->pageStyles,
                version: $this->version
            );
            
            if ($this->isNew) {
                // Criar nova página
                $result = $pageBuilderService->createPage($pageData->toArray(), auth()->id(), $publish);
            } else {
                // Atualizar página existente
                $result = $pageBuilderService->updatePage(
                    $this->pageSlug, 
                    $pageData->toArray(), 
                    auth()->id(), 
                    $publish
                );
            }
            
            if ($result) {
                $this->saveStatus = 'success';
                $this->saveMessage = $publish 
                    ? Translator::trans('page_published') 
                    : Translator::trans('page_saved_draft');
                
                // Atualizar slug se mudou
                if ($this->isNew || $this->pageSlug !== $this->slug) {
                    $oldSlug = $this->pageSlug;
                    $this->pageSlug = $this->slug;
                    $this->isNew = false;
                    
                    if ($oldSlug && $oldSlug !== $this->slug) {
                        // Redirecionar para novo slug
                        $this->redirect(route('pagebuilder.pages.edit', $this->slug));
                    }
                }
                
                // Recarregar versões
                $this->versions = array_map(function ($version) {
                    return json_decode(json_encode($version), true);
                }, $storage->listVersions($this->pageSlug));
            }
            
        } catch (PageValidationException $e) {
            $this->saveStatus = 'error';
            $this->saveMessage = $e->getFirstError();
            
        } catch (\Exception $e) {
            $this->saveStatus = 'error';
            $this->saveMessage = Translator::trans('save_error') . ': ' . $e->getMessage();
            logger()->error('Erro ao salvar página', ['error' => $e->getMessage()]);
        }
        
        $this->isSaving = false;
    }
    
    /**
     * Publica a página
     */
    public function publish()
    {
        $this->save(true);
    }
    
    /**
     * Salva como rascunho
     */
    public function saveDraft()
    {
        $this->save(false);
    }
    
    /**
     * Despublica a página
     */
    public function unpublish()
    {
        try {
            $pageBuilderService = app(PageBuilderService::class);
            $result = $pageBuilderService->unpublishPage($this->pageSlug, auth()->id());
            
            if ($result) {
                $this->published = false;
                session()->flash('message', Translator::trans('page_unpublished'));
                
                // Recarregar página para garantir dados consistentes
                $this->loadPage();
            }
            
        } catch (\Exception $e) {
            session()->flash('error', Translator::trans('unpublish_error') . ': ' . $e->getMessage());
            logger()->error('Erro ao despublicar página', ['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Deleta a página
     */
    public function delete()
    {
        if (!$this->pageSlug) {
            return;
        }
        
        try {
            $pageBuilderService = app(PageBuilderService::class);
            $result = $pageBuilderService->deletePage($this->pageSlug, auth()->id());
            
            if ($result) {
                session()->flash('message', Translator::trans('page_deleted'));
                $this->redirect(route('pagebuilder.pages.index'));
            }
            
        } catch (\Exception $e) {
            session()->flash('error', Translator::trans('delete_error') . ': ' . $e->getMessage());
            logger()->error('Erro ao deletar página', ['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Duplica a página
     */
    public function duplicate()
    {
        try {
            $storage = app(StorageInterface::class);
            $pageData = $storage->loadPage($this->pageSlug);
            
            if ($pageData) {
                // Criar novo slug
                $newSlug = $pageData->slug . '-copy-' . time();
                $newTitle = $pageData->title . ' (Cópia)';
                
                // Criar nova página com dados duplicados
                $newPageData = new PageData(
                    title: $newTitle,
                    slug: $newSlug,
                    content: $pageData->content,
                    published: false, // Sempre salvar como rascunho
                    headerEnabled: $pageData->headerEnabled,
                    footerEnabled: $pageData->footerEnabled,
                    customCss: $pageData->customCss,
                    customJs: $pageData->customJs,
                    theme: $pageData->theme,
                    styles: $pageData->styles
                );
                
                $pageBuilderService = app(PageBuilderService::class);
                $result = $pageBuilderService->createPage($newPageData->toArray(), auth()->id(), false);
                
                if ($result) {
                    session()->flash('message', Translator::trans('page_duplicated'));
                    $this->redirect(route('pagebuilder.pages.edit', $newSlug));
                }
            }
            
        } catch (\Exception $e) {
            session()->flash('error', Translator::trans('duplicate_error') . ': ' . $e->getMessage());
            logger()->error('Erro ao duplicar página', ['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Adiciona um bloco ao conteúdo
     */
    public function addBlock($blockType)
    {
        $blockManager = app(BlockManager::class);
        $blockClass = $blockManager->getBlockClass($blockType);
        
        if ($blockClass) {
            $this->content[] = [
                'type' => $blockType,
                'data' => $blockClass::defaults(),
                'styles' => []
            ];
            
            $this->selectedBlockIndex = count($this->content) - 1;
            
            // Criar versão após adicionar bloco
            $this->createVersion('Adicionado bloco: ' . $blockType);
        }
    }
    
    /**
     * Atualiza bloco quando modificado
     */
    #[On('block-updated')]
    public function onBlockUpdated($index, $data)
    {
        if (isset($this->content[$index])) {
            $this->content[$index]['data'] = $data;
            
            // Criar versão após modificação significativa
            $this->createVersion('Bloco atualizado');
        }
    }
    
    /**
     * Remove bloco
     */
    #[On('block-removed')]
    public function onBlockRemoved($index)
    {
        if (isset($this->content[$index])) {
            array_splice($this->content, $index, 1);
            $this->selectedBlockIndex = null;
            
            // Criar versão após remover bloco
            $this->createVersion('Bloco removido');
        }
    }
    
    /**
     * Move bloco
     */
    #[On('block-moved')]
    public function onBlockMoved($fromIndex, $toIndex)
    {
        if (isset($this->content[$fromIndex]) && isset($this->content[$toIndex])) {
            $block = $this->content[$fromIndex];
            array_splice($this->content, $fromIndex, 1);
            array_splice($this->content, $toIndex, 0, [$block]);
            
            // Criar versão após reordenar blocos
            $this->createVersion('Blocos reordenados');
        }
    }
    
    /**
     * Seleciona bloco
     */
    public function selectBlock($index)
    {
        $this->selectedBlockIndex = $index;
    }
    
    /**
     * Abre biblioteca de mídia
     */
    public function openMediaLibrary($field = null)
    {
        $this->mediaFieldActive = $field;
        $this->showMediaLibrary = true;
    }

    #[On('open-media-library')]
    public function handleOpenMediaLibrary($field)
    {
        $this->mediaFieldActive = $field;
        $this->showMediaLibrary = true;
    }
    
    /**
     * Manipula seleção de mídia
     */
    #[On('media-selected')]
    public function mediaSelected($url)
    {
        if ($this->mediaFieldActive) {
            // Encontrar o bloco ativo e atualizar o campo de mídia
            if ($this->selectedBlockIndex !== null && isset($this->content[$this->selectedBlockIndex])) {
                $this->content[$this->selectedBlockIndex]['data'][$this->mediaFieldActive] = $url;
                
                // Disparar atualização do bloco
                $this->dispatch('block-updated', 
                    index: $this->selectedBlockIndex, 
                    data: $this->content[$this->selectedBlockIndex]['data']
                );
            }
            $this->mediaFieldActive = null;
        }
        $this->showMediaLibrary = false;
    }

    /**
     * Abre editor de estilos
     */
    #[On('open-style-editor')]
    public function openStyleEditor()
    {
        $this->showStyleEditor = true;
    }

    /**
     * Aplica estilos do editor avançado
     */
    #[On('stylesApplied')]
    public function applyStyles($styles, $css)
    {
        $this->pageStyles = $styles;
        
        // Adicionar CSS customizado apenas se não estiver vazio
        if (!empty(trim($css))) {
            $this->customCss = $css . "\n" . $this->customCss;
        }
        
        $this->showStyleEditor = false;
        session()->flash('message', Translator::trans('styles_applied'));
        
        // Criar versão após aplicar estilos
        $this->createVersion('Estilos aplicados');
    }

    /**
     * Manipula mudança de tema
     */
    #[On('themeChanged')]
    public function onThemeChanged($theme)
    {
        $this->theme = $theme;
        
        // Criar versão após mudar tema
        $this->createVersion('Tema alterado para: ' . $theme);
    }

    /**
     * Atualiza propriedades automaticamente
     */
    public function updated($property, $value)
    {
        // Atualizar automaticamente quando propriedades importantes forem alteradas
        if (str_starts_with($property, 'styles.')) {
            $this->dispatch('stylesUpdated', $this->styles);
        }
        
        // Criar versão para mudanças significativas
        $significantProperties = ['title', 'slug', 'theme', 'customCss', 'customJs'];
        if (in_array($property, $significantProperties)) {
            $this->createVersion("Campo {$property} alterado");
        }
    }
    
    /**
     * Cria uma versão da página
     */
    protected function createVersion(string $note = null): void
    {
        if (!$this->pageSlug) {
            return;
        }
        
        try {
            $storage = app(StorageInterface::class);
            $storage->createVersion(
                $this->pageSlug, 
                'revision', 
                auth()->id(), 
                $note
            );
            
            // Atualizar lista de versões
            $this->versions = array_map(function ($version) {
                return json_decode(json_encode($version), true);
            }, $storage->listVersions($this->pageSlug));

            
        } catch (\Exception $e) {
            logger()->error('Erro ao criar versão', ['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Abre histórico de versões
     */
    public function showVersionHistory()
    {
        try {
            $storage = app(StorageInterface::class);
            $this->versions = array_map(function ($version) {
                return json_decode(json_encode($version), true);
            }, $storage->listVersions($this->pageSlug));

            $this->showVersionHistory = true;
            
        } catch (\Exception $e) {
            session()->flash('error', Translator::trans('load_versions_error'));
            logger()->error('Erro ao carregar versões', ['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Restaura uma versão específica
     */
    public function restoreVersion($versionId)
    {
        try {
            $pageBuilderService = app(PageBuilderService::class);
            $result = $pageBuilderService->restoreVersion($this->pageSlug, $versionId, auth()->id());
            
            if ($result) {
                session()->flash('message', Translator::trans('version_restored'));
                $this->showVersionHistory = false;
                
                // Recarregar página com dados restaurados
                $this->loadPage();
                
                // Disparar evento para outros componentes
                $this->dispatch('versionRestored', $versionId);
            }
            
        } catch (\Exception $e) {
            session()->flash('error', Translator::trans('restore_error') . ': ' . $e->getMessage());
            logger()->error('Erro ao restaurar versão', ['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Exporta a página
     */
    public function export()
    {
        try {
            $pageData = new PageData(
                title: $this->title,
                slug: $this->slug,
                content: $this->content,
                published: $this->published,
                headerEnabled: $this->headerEnabled,
                footerEnabled: $this->footerEnabled,
                customCss: $this->customCss,
                customJs: $this->customJs,
                theme: $this->theme,
                styles: $this->pageStyles,
                version: $this->version
            );
            
            $filename = "page-{$this->slug}-" . date('Y-m-d') . ".json";
            
            return response()->streamDownload(
                function () use ($pageData) {
                    echo $pageData->toJson();
                },
                $filename,
                [
                    'Content-Type' => 'application/json',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ]
            );
            
        } catch (\Exception $e) {
            session()->flash('error', Translator::trans('export_error') . ': ' . $e->getMessage());
            logger()->error('Erro ao exportar página', ['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Importa conteúdo para a página
     */
    public function import($file)
    {
        try {
            $content = file_get_contents($file->getRealPath());
            $data = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Arquivo JSON inválido');
            }
            
            // Validar estrutura básica
            if (!isset($data['content']) || !is_array($data['content'])) {
                throw new \Exception('Estrutura de arquivo inválida');
            }
            
            // Aplicar conteúdo importado
            $this->content = $data['content'];
            
            // Aplicar estilos se existirem
            if (isset($data['styles'])) {
                $this->pageStyles = $data['styles'];
            }
            
            // Aplicar CSS customizado se existir
            if (isset($data['custom_css'])) {
                $this->customCss = $data['custom_css'];
            }
            
            session()->flash('message', Translator::trans('import_success'));
            
            // Criar versão após importação
            $this->createVersion('Conteúdo importado');
            
        } catch (\Exception $e) {
            session()->flash('error', Translator::trans('import_error') . ': ' . $e->getMessage());
            logger()->error('Erro ao importar página', ['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Pré-visualiza a página
     */
    public function preview()
    {
        // Salvar primeiro como rascunho
        $this->saveDraft();
        
        // Abrir pré-visualização em nova aba
        $previewUrl = route('pagebuilder.pages.preview', $this->slug);
        $this->dispatch('openPreview', $previewUrl);
    }
    
    /**
     * Limpa o cache da página
     */
    public function clearCache()
    {
        try {
            $pageBuilderService = app(PageBuilderService::class);
            $pageBuilderService->clearCache($this->pageSlug);
            
            session()->flash('message', Translator::trans('cache_cleared'));
            
        } catch (\Exception $e) {
            session()->flash('error', Translator::trans('cache_error') . ': ' . $e->getMessage());
            logger()->error('Erro ao limpar cache', ['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Renderiza o componente
     */
    public function render()
    {
        return view('pagebuilder::livewire.page-builder-editor', [
            'availableBlocks' => app(BlockManager::class)->getAvailableBlocks(),
            'themeOptions' => $this->themeOptions,
            'isDirty' => $this->isDirty(),
            'hasPublishedVersion' => $this->hasPublishedVersion(),
        ]);
    }
    
    /**
     * Verifica se há alterações não salvas
     */
    protected function isDirty(): bool
    {
        if ($this->isNew) {
            return !empty($this->title) || !empty($this->content);
        }
        
        // Em uma implementação real, você compararia com os dados originais
        return true;
    }
    
    /**
     * Verifica se existe versão publicada
     */
    protected function hasPublishedVersion(): bool
    {
        if (!$this->pageSlug) {
            return false;
        }
        
        try {
            $storage = app(StorageInterface::class);
            $publishedPath = storage_path("app/pagebuilder/{$this->pageSlug}.json");
            return file_exists($publishedPath);
            
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Reseta o formulário
     */
    public function resetForm()
    {
        if ($this->isNew) {
            $this->initializeNewPage();
        } else {
            $this->loadPage();
        }
        
        session()->flash('message', Translator::trans('form_reset'));
    }
    
    /**
     * Validação em tempo real do slug
     */
    public function updatedSlug($value)
    {
        $this->validateOnly('slug', [
            'slug' => [
                'required',
                'alpha_dash',
                'max:100',
                new UniquePageSlug('page', $this->pageSlug)
            ],
        ]);
    }
}