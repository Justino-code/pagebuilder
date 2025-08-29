<?php

namespace Justino\PageBuilder\Http\Livewire\Components;

use Livewire\Component;

class Toolbar extends Component
{
    public $activeTab = 'content';
    public $saveMessage = '';
    public $saveStatus = '';
    
    protected $listeners = [
        'saveStatusUpdated' => 'updateSaveStatus',
        'tabChanged' => 'changeTab'
    ];
    
    public function changeTab($tab)
    {
        $this->activeTab = $tab;
    }
    
    public function updateSaveStatus($message, $status)
    {
        $this->saveMessage = $message;
        $this->saveStatus = $status;
    }
    
    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->dispatch('tabSwitched', tab: $tab);
    }
    
    public function performAction($action)
    {
        $this->dispatch('toolbarAction', action: $action);
    }
    
    public function render()
    {
        return view('pagebuilder::livewire.components.toolbar');
    }
}