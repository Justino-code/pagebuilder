<?php

namespace Justino\PageBuilder\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Justino\PageBuilder\Contracts\StorageInterface;
use Justino\PageBuilder\Services\PageBuilderService;
use Justino\PageBuilder\Services\BlockManager;
use Justino\PageBuilder\Services\BlockRenderer;
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
        'versionRestored',
        'blockSelected',
        'blockUpdated',
        'blockRemoved',
        'blockMoved'
    ];

    public function mount($pageSlug = null)
    {
        $this->pageSlug = $pageSlug;
        $this->isNew = is_null($pageSlug);
        
        if (!$this->isNew) {
            $this->loadPage();
        } else {
            $this->initializeNewPage();
        }
    }
    
    protected function loadPage(): void
    {
        $this->isLoading = true;
        
        try {
            $storage = app(StorageInterface::class);
            $pageData = $storage->loadPage($this->pageSlug);
            
            if (!$pageData) {
                session()->flash('error', Translator::trans('messages.page_not_found'));
                $this->redirect(route('pagebuilder.pages.index'));
                return;
            }
            
            $this->title = $pageData->title;
            $this->slug = $pageData->slug;
            $this->published = $pageData->published;
            $this->content = $this->validateContentArray($pageData->content);
            $this->customCss = $pageData->customCss;
            $this->customJs = $pageData->customJs;
            $this->headerEnabled = $pageData->headerEnabled;
            $this->footerEnabled = $pageData->footerEnabled;
            $this->theme = $pageData->theme;
            $this->styles = $pageData->styles;
            $this->version = $pageData->version;
            
            $this->versions = array_map(function ($version) {
                return json_decode(json_encode($version), true);
            }, $storage->listVersions($this->pageSlug));
            
        } catch (\Exception $e) {
            session()->flash('error', Translator::trans('messages.load_error') . ': ' . $e->getMessage());
            logger()->error('Erro ao carregar página', ['error' => $e->getMessage()]);
        } finally {
            $this->isLoading = false;
        }
    }
    
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
                $result = $pageBuilderService->createPage($pageData->toArray(), auth()->id(), $publish);
            } else {
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
                    ? Translator::trans('messages.page_published') 
                    : Translator::trans('messages.page_saved_draft');
                
                if ($this->isNew || $this->pageSlug !== $this->slug) {
                    $oldSlug = $this->pageSlug;
                    $this->pageSlug = $this->slug;
                    $this->isNew = false;
                    
                    if ($oldSlug && $oldSlug !== $this->slug) {
                        $this->redirect(route('pagebuilder.pages.edit', $this->slug));
                    }
                }
                
                $storage = app(StorageInterface::class);
                $this->versions = array_map(function ($version) {
                    return json_decode(json_encode($version), true);
                }, $storage->listVersions($this->pageSlug));
            }
            
        } catch (PageValidationException $e) {
            $this->saveStatus = 'error';
            $this->saveMessage = $e->getFirstError();
            
        } catch (\Exception $e) {
            $this->saveStatus = 'error';
            $this->saveMessage = Translator::trans('messages.save_error') . ': ' . $e->getMessage();
            logger()->error('Erro ao salvar página', ['error' => $e->getMessage()]);
        }
        
        $this->isSaving = false;
    }
    
    public function publish()
    {
        $this->save(true);
    }
    
    public function saveDraft()
    {
        $this->save(false);
    }
    
    public function unpublish()
    {
        try {
            $pageBuilderService = app(PageBuilderService::class);
            $result = $pageBuilderService->unpublishPage($this->pageSlug, auth()->id());
            
            if ($result) {
                $this->published = false;
                session()->flash('message', Translator::trans('page_unpublished'));
                $this->loadPage();
            }
            
        } catch (\Exception $e) {
            session()->flash('error', Translator::trans('unpublish_error') . ': ' . $e->getMessage());
            logger()->error('Erro ao despublicar página', ['error' => $e->getMessage()]);
        }
    }
    
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
    
    public function duplicate()
    {
        try {
            $storage = app(StorageInterface::class);
            $pageData = $storage->loadPage($this->pageSlug);
            
            if ($pageData) {
                $newSlug = $pageData->slug . '-copy-' . time();
                $newTitle = $pageData->title . ' (Cópia)';
                
                $newPageData = new PageData(
                    title: $newTitle,
                    slug: $newSlug,
                    content: $pageData->content,
                    published: false,
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
    
    public function addBlock($blockType)
    {
        try {
            $blockManager = app(BlockManager::class);
            
            if (!$blockManager->isValidBlockType($blockType)) {
                throw new InvalidArgumentException("Tipo de bloco inválido: {$blockType}");
            }
            
            $blockClass = $blockManager->getBlockClassName($blockType);
            
            $this->content[] = [
                'type' => $blockType,
                'data' => $blockClass::defaults(),
                'styles' => []
            ];
            
            $this->selectedBlockIndex = count($this->content) - 1;
            $this->createVersion('Adicionado bloco: ' . $blockType);
            
        } catch (\Exception $e) {
            session()->flash('error', "Erro ao adicionar bloco: " . $e->getMessage());
            Log::error('Add block error: ' . $e->getMessage());
        }
    }
    
    #[On('block-updated')]
    public function onBlockUpdated($index, $data)
    {
        if (isset($this->content[$index])) {
            $this->content[$index]['data'] = $data;
            $this->createVersion('Bloco atualizado');
        }
    }
    
    #[On('block-removed')]
    public function onBlockRemoved($index)
    {
        if (isset($this->content[$index])) {
            array_splice($this->content, $index, 1);
            $this->selectedBlockIndex = null;
            $this->createVersion('Bloco removido');
        }
    }
    
    #[On('block-moved')]
    public function onBlockMoved($fromIndex, $toIndex)
    {
        if (isset($this->content[$fromIndex]) && isset($this->content[$toIndex])) {
            $block = $this->content[$fromIndex];
            array_splice($this->content, $fromIndex, 1);
            array_splice($this->content, $toIndex, 0, [$block]);
            $this->createVersion('Blocos reordenados');
        }
    }
    
    #[On('block-selected')]
    public function onBlockSelected($index)
    {
        $this->selectedBlockIndex = $index;
    }
    
    public function selectBlock($index)
    {
        $this->selectedBlockIndex = $index;
    }
    
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
    
    #[On('media-selected')]
    public function mediaSelected($url)
    {
        if ($this->mediaFieldActive && $this->selectedBlockIndex !== null) {
            $fieldPath = $this->mediaFieldActive;
            $keys = explode('.', $fieldPath);
            
            if (count($keys) === 1) {
                $this->content[$this->selectedBlockIndex]['data'][$keys[0]] = $url;
            } elseif (count($keys) === 3) {
                list($parent, $index, $field) = $keys;
                if (isset($this->content[$this->selectedBlockIndex]['data'][$parent][$index])) {
                    $this->content[$this->selectedBlockIndex]['data'][$parent][$index][$field] = $url;
                }
            }
            
            $this->dispatch('block-updated', 
                index: $this->selectedBlockIndex, 
                data: $this->content[$this->selectedBlockIndex]['data']
            );
            
            $this->mediaFieldActive = null;
        }
        $this->showMediaLibrary = false;
    }

    #[On('open-style-editor')]
    public function openStyleEditor()
    {
        $this->showStyleEditor = true;
    }

    #[On('stylesApplied')]
    public function applyStyles($styles, $css)
    {
        $this->pageStyles = $styles;
        
        if (!empty(trim($css))) {
            $this->customCss = $css . "\n" . $this->customCss;
        }
        
        $this->showStyleEditor = false;
        session()->flash('message', Translator::trans('styles_applied'));
        $this->createVersion('Estilos aplicados');
    }

    #[On('themeChanged')]
    public function onThemeChanged($theme)
    {
        $this->theme = $theme;
        $this->createVersion('Tema alterado para: ' . $theme);
    }

    public function updated($property, $value)
    {
        if (str_starts_with($property, 'styles.')) {
            $this->dispatch('stylesUpdated', $this->styles);
        }
        
        $significantProperties = ['title', 'slug', 'theme', 'customCss', 'customJs'];
        if (in_array($property, $significantProperties)) {
            $this->createVersion("Campo {$property} alterado");
        }
    }
    
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
            
            $this->versions = array_map(function ($version) {
                return json_decode(json_encode($version), true);
            }, $storage->listVersions($this->pageSlug));
            
        } catch (\Exception $e) {
            logger()->error('Erro ao criar versão', ['error' => $e->getMessage()]);
        }
    }
    
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
    
    public function restoreVersion($versionId)
    {
        try {
            $pageBuilderService = app(PageBuilderService::class);
            $result = $pageBuilderService->restoreVersion($this->pageSlug, $versionId, auth()->id());
            
            if ($result) {
                session()->flash('message', Translator::trans('version_restored'));
                $this->showVersionHistory = false;
                $this->loadPage();
                $this->dispatch('versionRestored', $versionId);
            }
            
        } catch (\Exception $e) {
            session()->flash('error', Translator::trans('restore_error') . ': ' . $e->getMessage());
            logger()->error('Erro ao restaurar versão', ['error' => $e->getMessage()]);
        }
    }
    
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
                ]
            );
            
        } catch (\Exception $e) {
            session()->flash('error', Translator::trans('export_error') . ': ' . $e->getMessage());
            logger()->error('Erro ao exportar página', ['error' => $e->getMessage()]);
        }
    }
    
    public function import($file)
    {
        try {
            $content = file_get_contents($file->getRealPath());
            $data = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Arquivo JSON inválido');
            }
            
            if (!isset($data['content']) || !is_array($data['content'])) {
                throw new \Exception('Estrutura de arquivo inválida');
            }
            
            $this->content = $data['content'];
            
            if (isset($data['styles'])) {
                $this->pageStyles = $data['styles'];
            }
            
            if (isset($data['custom_css'])) {
                $this->customCss = $data['custom_css'];
            }
            
            session()->flash('message', Translator::trans('import_success'));
            $this->createVersion('Conteúdo importado');
            
        } catch (\Exception $e) {
            session()->flash('error', Translator::trans('import_error') . ': ' . $e->getMessage());
            logger()->error('Erro ao importar página', ['error' => $e->getMessage()]);
        }
    }
    
    public function preview()
    {
        if($this->slug === ''){
            return;
        }
        $this->saveDraft();
        $previewUrl = route('pagebuilder.pages.preview', $this->slug);
        $this->dispatch('openPreview', $previewUrl);
    }
    
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
    
    public function render()
    {
        $blockManager = app(BlockManager::class);
        
        return view('pagebuilder::livewire.page-builder-editor', [
            'availableBlocks' => $blockManager->getAvailableBlocks(),
            'themeOptions' => $this->themeOptions,
            'isDirty' => $this->isDirty(),
            'hasPublishedVersion' => $this->hasPublishedVersion(),
        ]);
    }
    
    protected function isDirty(): bool
    {
        if ($this->isNew) {
            return !empty($this->title) || !empty($this->content);
        }
        return true;
    }
    
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
    
    public function resetForm()
    {
        if ($this->isNew) {
            $this->initializeNewPage();
        } else {
            $this->loadPage();
        }
        
        session()->flash('message', Translator::trans('form_reset'));
    }
    
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

    protected function validateContentArray(array $content): array
    {
        $validatedContent = [];
        
        foreach ($content as $index => $block) {
            try {
                if (!isset($block['type'])) {
                    throw new InvalidArgumentException("Bloco sem tipo no índice {$index}");
                }
                
                if (!is_array($block['data'] ?? [])) {
                    $block['data'] = [];
                }
                
                if (!is_array($block['styles'] ?? [])) {
                    $block['styles'] = [];
                }
                
                $blockManager = app(BlockManager::class);
                
                if (!$blockManager->isValidBlockType($block['type'])) {
                    Log::warning("Tipo de bloco inválido encontrado", [
                        'index' => $index,
                        'type' => $block['type']
                    ]);
                    continue; // Pula blocos inválidos
                }
                
                $validatedContent[] = $block;
                
            } catch (\Exception $e) {
                Log::error("Erro ao validar bloco {$index}: " . $e->getMessage(), [
                    'block_data' => $block
                ]);
            }
        }
        
        return $validatedContent;
    }    
}