<?php

namespace Justino\PageBuilder\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Justino\PageBuilder\Services\JsonPageStorage;
use Justino\PageBuilder\Services\BlockManager;
use Justino\PageBuilder\Helpers\Translator;
use Justino\PageBuilder\Rules\UniquePageSlug;
use Livewire\Attributes\On;

use Justino\PageBuilder\DTOs\PageData;

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
    
    // UI State
    public $activeTab = 'content';
    public $selectedBlockIndex = null;
    public $showMediaLibrary = false;
    public $mediaFieldActive = null;

    // Styles
    public $showStyleEditor = false;
    public $pageStyles = [];
    
    protected $listeners = [
        'mediaSelected'
    ];

    public function mount($pageSlug = null, $pageData = [])
    {
        $this->pageSlug = $pageSlug;
        $this->isNew = is_null($pageSlug);
        
        if (!$this->isNew && !empty($pageData)) {
            $this->loadPageData($pageData);
        }
    }
    
    public function loadPageData($pageData)
    {
        $this->title = $pageData['title'] ?? '';
        $this->slug = $pageData['slug'] ?? $this->pageSlug;
        $this->published = $pageData['published'] ?? false;
        $this->content = $pageData['content'] ?? [];
        $this->customCss = $pageData['custom_css'] ?? '';
        $this->customJs = $pageData['custom_js'] ?? '';
        $this->headerEnabled = $pageData['header_enabled'] ?? true;
        $this->footerEnabled = $pageData['footer_enabled'] ?? true;
    }

public function save()
{
    $this->validate([
        'title' => 'required|string|max:255',
        'slug' => [
            'required',
            'alpha_dash',
            new UniquePageSlug('page', $this->pageSlug)
        ],
    ]);
    
    $storage = app(JsonPageStorage::class);
    
    // Criar DTO
    $pageData = new PageData(
        title: $this->title,
        slug: $this->slug,
        content: $this->content,
        published: $this->published,
        headerEnabled: $this->headerEnabled,
        footerEnabled: $this->footerEnabled,
        customCss: $this->customCss,
        customJs: $this->customJs
    );
    
    // Salvar usando DTO
    $storage->savePage($pageData);
    
    if ($this->pageSlug !== $this->slug) {
        if ($this->pageSlug) {
            $storage->delete($this->pageSlug);
        }
        
        $this->pageSlug = $this->slug;
        $this->isNew = false;
        return redirect()->route('pagebuilder.pages.edit', $this->slug);
    }
    
    $this->isNew = false;
    session()->flash('message', Translator::trans('page_updated'));
}
    
    public function delete()
    {
        if ($this->pageSlug) {
            $storage = app(JsonPageStorage::class);
            $storage->delete($this->pageSlug);
            return redirect()->route('pagebuilder.pages.index');
        }
    }
    
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
        }
    }
    
    #[On('block-updated')]
    public function onBlockUpdated($index, $data)
    {
        if (isset($this->content[$index])) {
            $this->content[$index]['data'] = $data;
        }
    }
    
    #[On('block-removed')]
    public function onBlockRemoved($index)
    {
        if (isset($this->content[$index])) {
            array_splice($this->content, $index, 1);
            $this->selectedBlockIndex = null;
        }
    }
    
    #[On('block-moved')]
    public function onBlockMoved($fromIndex, $toIndex)
    {
        if (isset($this->content[$fromIndex]) && isset($this->content[$toIndex])) {
            $block = $this->content[$fromIndex];
            array_splice($this->content, $fromIndex, 1);
            array_splice($this->content, $toIndex, 0, [$block]);
        }
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
        if ($this->mediaFieldActive) {
            // Encontrar o bloco ativo e atualizar o campo de mídia
            if ($this->selectedBlockIndex !== null && isset($this->content[$this->selectedBlockIndex])) {
                $this->content[$this->selectedBlockIndex]['data'][$this->mediaFieldActive] = $url;
            }
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
        $this->customCss = $css . "\n" . $this->customCss;
        $this->showStyleEditor = false;
        
        session()->flash('message', Translator::trans('styles_applied'));
    }

    public function selectElement()
    {
        $this->dispatch('elementSelected', 
            type: $this->block['type'],
            id: $this->index
        );
    }
    
    public function render()
    {
        return view('pagebuilder::livewire.page-builder-editor', [
            'availableBlocks' => app(BlockManager::class)->getAvailableBlocks()
        ]);
    }
}