<?php

namespace Justino\PageBuilder\Http\Livewire\Components;

use Livewire\Component;

class ContentArea extends Component
{
    public $activeTab = 'content';
    public $content = [];
    public $customCss = '';
    public $customJs = '';
    public $versions = [];
    
    protected $listeners = [
        'tabSwitched' => 'switchTab',
        'contentUpdated' => 'updateContent',
        'customCssUpdated' => 'updateCustomCss',
        'customJsUpdated' => 'updateCustomJs',
        'versionsUpdated' => 'updateVersions'
    ];
    
    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }
    
    public function updateContent($content)
    {
        $this->content = $content;
    }
    
    public function updateCustomCss($css)
    {
        $this->customCss = $css;
    }
    
    public function updateCustomJs($js)
    {
        $this->customJs = $js;
    }
    
    public function updateVersions($versions)
    {
        $this->versions = $versions;
    }
    
    public function selectBlock($index)
    {
        $this->dispatch('blockSelected', index: $index);
    }
    
    public function render()
    {
        return view('pagebuilder::livewire.components.content-area');
    }
}