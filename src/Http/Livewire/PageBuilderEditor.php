<?php

namespace Justino\PageBuilder\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Justino\PageBuilder\Services\JsonPageStorage;
use Justino\PageBuilder\Services\BlockManager;
use Justino\PageBuilder\Helpers\Translator;

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
    
    protected $listeners = [
        'blockUpdated', 'blockRemoved', 'blockMoved', 
        'mediaSelected', 'savePage', 'deletePage'
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
            'slug' => 'required|alpha_dash|unique_page_slug:page,' . $this->pageSlug,
        ]);
        
        $storage = app(JsonPageStorage::class);
        
        $pageData = [
            'type' => 'page',
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'published' => $this->published,
            'custom_css' => $this->customCss,
            'custom_js' => $this->customJs,
            'header_enabled' => $this->headerEnabled,
            'footer_enabled' => $this->footerEnabled,
            'updated_at' => now()->toISOString()
        ];
        
        if ($this->isNew) {
            $pageData['created_at'] = now()->toISOString();
        }
        
        $storage->save($pageData);
        
        // Se o slug mudou, redirecionar
        if ($this->pageSlug !== $this->slug) {
            $this->pageSlug = $this->slug;
            $this->isNew = false;
            return redirect()->route('pagebuilder.pages.edit', $this->slug);
        }
        
        $this->isNew = false;
        $this->emit('pageSaved', $this->slug);
        session()->flash('message', Translator::trans('page_updated'));
    }
    
    public function delete()
    {
        if ($this->pageSlug) {
            $storage = app(JsonPageStorage::class);
            $storage->delete($this->pageSlug);
            
            $this->emit('pageDeleted');
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
    
    public function onBlockUpdated($index, $data)
    {
        if (isset($this->content[$index])) {
            $this->content[$index]['data'] = $data;
        }
    }
    
    public function onBlockRemoved($index)
    {
        if (isset($this->content[$index])) {
            array_splice($this->content, $index, 1);
            $this->selectedBlockIndex = null;
        }
    }
    
    public function onBlockMoved($fromIndex, $toIndex)
    {
        $block = $this->content[$fromIndex];
        array_splice($this->content, $fromIndex, 1);
        array_splice($this->content, $toIndex, 0, [$block]);
    }
    
    public function selectBlock($index)
    {
        $this->selectedBlockIndex = $index;
    }
    
    public function openMediaLibrary()
    {
        $this->showMediaLibrary = true;
    }
    
    public function onMediaSelected($url)
    {
        $this->emitTo('page-builder-block', 'mediaSelected', $url);
        $this->showMediaLibrary = false;
    }
    
    public function render()
    {
        return view('pagebuilder::livewire.page-builder-editor', [
            'availableBlocks' => app(BlockManager::class)->getAvailableBlocks()
        ]);
    }
}