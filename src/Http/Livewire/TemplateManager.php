<?php

namespace Justino\PageBuilder\Http\Livewire;

use Livewire\Component;
use Justino\PageBuilder\Services\JsonPageStorage;
use Justino\PageBuilder\Helpers\Translator;

class TemplateManager extends Component
{
    public $templates = [];
    public $templateType = 'header';
    public $showDeleteModal = false;
    public $templateToDelete = null;
    
    public function mount($type = 'header')
    {
        $this->templateType = $type;
        $this->loadTemplates();
    }
    
    public function loadTemplates()
    {
        $storage = app(JsonPageStorage::class);
        $this->templates = $storage->all($this->templateType);
    }
    
    public function createTemplate()
    {
        return redirect()->route('pagebuilder.templates.edit', [
            'type' => $this->templateType
        ]);
    }
    
    public function editTemplate($slug)
    {
        return redirect()->route('pagebuilder.templates.edit', [
            'type' => $this->templateType,
            'slug' => $slug
        ]);
    }
    
    public function confirmDelete($slug)
    {
        $this->templateToDelete = $slug;
        $this->showDeleteModal = true;
    }
    
    public function deleteTemplate()
    {
        if ($this->templateToDelete) {
            $storage = app(JsonPageStorage::class);
            $storage->delete($this->templateToDelete);
            
            $this->showDeleteModal = false;
            $this->templateToDelete = null;
            $this->loadTemplates();
            
            session()->flash('message', Translator::trans('template_deleted'));
        }
    }
    
    public function setDefault($slug)
    {
        $storage = app(JsonPageStorage::class);
        
        // Remover default de todos os templates
        $templates = $storage->all($this->templateType);
        foreach ($templates as $template) {
            if ($template['is_default'] ?? false) {
                $template['is_default'] = false;
                $storage->save($template);
            }
        }
        
        // Definir novo template como default
        $template = $storage->find($slug, $this->templateType);
        if ($template) {
            $template['is_default'] = true;
            $template['updated_at'] = now()->toISOString();
            $storage->save($template);
            
            $this->loadTemplates();
            session()->flash('message', Translator::trans('template_set_default'));
        }
    }
    
    public function render()
    {
        return view('pagebuilder::livewire.template-manager');
    }
}