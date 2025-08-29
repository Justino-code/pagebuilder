<?php

namespace Justino\PageBuilder\Http\Livewire\Components;

use Livewire\Component;
use Justino\PageBuilder\Services\BlockManager;

class Sidebar extends Component
{
    public $title;
    public $slug;
    public $published;
    public $theme;
    public $headerEnabled;
    public $footerEnabled;
    public $isNew;
    public $version;
    public $isSaving;
    public $saveStatus;
    
    public $themeOptions = [
        'system' => 'Sistema',
        'light' => 'Claro', 
        'dark' => 'Escuro'
    ];
    
    protected $listeners = [
        'pageUpdated' => 'refreshData',
        'savingStatusChanged' => 'updateSavingStatus'
    ];
    
    public function mount($pageData, $isNew)
    {
        $this->isNew = $isNew;
        $this->refreshData($pageData);
    }
    
    public function refreshData($pageData)
    {
        $this->title = $pageData['title'] ?? '';
        $this->slug = $pageData['slug'] ?? '';
        $this->published = $pageData['published'] ?? false;
        $this->theme = $pageData['theme'] ?? 'system';
        $this->headerEnabled = $pageData['headerEnabled'] ?? true;
        $this->footerEnabled = $pageData['footerEnabled'] ?? true;
        $this->version = $pageData['version'] ?? '1.0.0';
    }
    
    public function updateSavingStatus($isSaving, $saveStatus)
    {
        $this->isSaving = $isSaving;
        $this->saveStatus = $saveStatus;
    }
    
    public function updated($property, $value)
    {
        $this->dispatch('sidebarFieldUpdated', property: $property, value: $value);
    }
    
    public function addBlock($blockType)
    {
        $this->dispatch('addBlockRequested', blockType: $blockType);
    }
    
    public function performAction($action)
    {
        $this->dispatch('sidebarAction', action: $action);
    }
    
    public function render()
    {
        return view('pagebuilder::livewire.components.sidebar', [
            'availableBlocks' => app(BlockManager::class)->getAvailableBlocks()
        ]);
    }
}