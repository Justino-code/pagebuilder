<?php

namespace Justino\PageBuilder\Http\Livewire;

use Livewire\Component;
use Justino\PageBuilder\Services\JsonPageStorage;
use Justino\PageBuilder\Services\BlockManager;

class TemplateManager extends Component
{
    public $templates = [];
    public $templateType = 'header';
    public $editingTemplate = null;
    public $showEditor = false;
    
    protected $listeners = ['templateSaved' => 'loadTemplates'];
    
    public function mount($type = 'header')
    {
        $this->templateType = $type;
        $this->loadTemplates();
    }
    
    public function loadTemplates()
    {
        $this->templates = app(JsonPageStorage::class)->all($this->templateType);
    }
    
    public function createTemplate()
    {
        $blockManager = app(BlockManager::class);
        $blockClass = $blockManager->getBlockClass($this->templateType);
        
        if ($blockClass) {
            $this->editingTemplate = array_merge(
                ['type' => $this->templateType],
                $blockClass::defaults()
            );
            $this->showEditor = true;
        }
    }
    
    public function editTemplate($slug)
    {
        $this->editingTemplate = app(JsonPageStorage::class)->find($slug, $this->templateType);
        $this->showEditor = true;
    }
    
    public function deleteTemplate($slug)
    {
        app(JsonPageStorage::class)->delete($slug);
        $this->loadTemplates();
        session()->flash('message', 'Template deleted successfully.');
    }
    
    public function setDefault($slug)
    {
        $templates = $this->templates;
        
        foreach ($templates as &$template) {
            $template['is_default'] = ($template['slug'] === $slug);
            app(JsonPageStorage::class)->save($template);
        }
        
        $this->loadTemplates();
        session()->flash('message', 'Default template set successfully.');
    }
    
    public function render()
    {
        return view('pagebuilder::livewire.template-manager');
    }
}