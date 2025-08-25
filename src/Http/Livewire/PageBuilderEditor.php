<?php

namespace Justino\PageBuilder\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Justino\PageBuilder\Models\Page;
use Justino\PageBuilder\Services\BlockManager;

class PageBuilderEditor extends Component
{
    use WithFileUploads;
    
    public $page;
    public $blocks = [];
    public $title;
    public $slug;
    public $published = false;
    public $customCss = '';
    public $customJs = '';
    public $headerEnabled = true;
    public $footerEnabled = true;
    public $activeTab = 'content';
    public $selectedBlockIndex = null;
    public $showMediaLibrary = false;
    
    protected $listeners = [
        'blockUpdated' => 'onBlockUpdated',
        'blockRemoved' => 'onBlockRemoved',
        'blockMoved' => 'onBlockMoved',
        'mediaSelected' => 'onMediaSelected'
    ];
    
    public function mount($pageId = null)
    {
        if ($pageId) {
            $this->page = Page::findOrFail($pageId);
            $this->title = $this->page->title;
            $this->slug = $this->page->slug;
            $this->published = $this->page->published;
            $this->customCss = $this->page->custom_css;
            $this->customJs = $this->page->custom_js;
            $this->headerEnabled = $this->page->header_enabled;
            $this->footerEnabled = $this->page->footer_enabled;
            $this->blocks = $this->page->content ?? [];
        } else {
            $this->page = new Page();
            $this->blocks = [];
        }
    }
    
    public function render()
    {
        return view('pagebuilder::livewire.page-builder-editor', [
            'availableBlocks' => app(BlockManager::class)->getAvailableBlocks()
        ]);
    }
    
    public function addBlock($blockType)
    {
        $blockManager = app(BlockManager::class);
        $blockClass = $blockManager->getBlockClass($blockType);
        
        if ($blockClass) {
            $this->blocks[] = [
                'type' => $blockType,
                'data' => $blockClass::defaults(),
                'styles' => []
            ];
            
            $this->selectedBlockIndex = count($this->blocks) - 1;
        }
    }
    
    public function onBlockUpdated($index, $data)
    {
        if (isset($this->blocks[$index])) {
            $this->blocks[$index]['data'] = $data;
        }
    }
    
    public function onBlockRemoved($index)
    {
        if (isset($this->blocks[$index])) {
            array_splice($this->blocks, $index, 1);
            $this->selectedBlockIndex = null;
        }
    }
    
    public function onBlockMoved($fromIndex, $toIndex)
    {
        $block = $this->blocks[$fromIndex];
        array_splice($this->blocks, $fromIndex, 1);
        array_splice($this->blocks, $toIndex, 0, [$block]);
    }
    
    public function selectBlock($index)
    {
        $this->selectedBlockIndex = $index;
    }
    
    public function savePage()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|alpha_dash|unique:pages,slug,' . ($this->page->id ?? 'NULL'),
        ]);
        
        $pageData = [
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->blocks,
            'published' => $this->published,
            'custom_css' => $this->customCss,
            'custom_js' => $this->customJs,
            'header_enabled' => $this->headerEnabled,
            'footer_enabled' => $this->footerEnabled,
        ];
        
        if ($this->page->id) {
            $this->page->update($pageData);
        } else {
            $this->page = Page::create($pageData);
        }
        
        session()->flash('message', 'Page saved successfully.');
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
}