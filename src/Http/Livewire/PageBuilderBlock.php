<?php

namespace Justino\PageBuilder\Http\Livewire;

use Livewire\Component;
use Justino\PageBuilder\Services\BlockManager;
use Justino\PageBuilder\Helpers\Translator;
use Livewire\Attributes\On;

class PageBuilderBlock extends Component
{
    public $index;
    public $block;
    public $isSelected = false;
    public $editing = false;
    public $blockLabel = 'Block';
    public $blockIcon = '📦';
    public $blockSchema = [];
    public $mediaFieldActive = null;
    
    public function mount($index, $block, $isSelected = false)
    {
        $this->index = $index;
        $this->block = $block;
        $this->isSelected = $isSelected;
        $this->loadBlockMetadata();
    }
    
    public function loadBlockMetadata()
    {
        $blockManager = app(BlockManager::class);
        $blockClass = $blockManager->getBlockClass($this->block['type']);
        
        if ($blockClass) {
            $this->blockLabel = $blockManager->getBlockLabelSlug($blockClass::label());
            $this->blockIcon = $blockClass::icon();
            $this->blockSchema = $blockClass::schema();
        }
    }
    
    public function render()
    {

        return view('pagebuilder::livewire.page-builder-block', [
            'blockLabel' => $this->blockLabel,
            'blockIcon' => $this->blockIcon,
            'blockSchema' => $this->blockSchema
        ]);
    }
    
    // ADICIONE ESTE MÉTODO
    public function selectBlock()
    {
        $this->isSelected = true;
        $this->dispatch('block-selected', index: $this->index);
    }
    
    public function edit()
    {
        $this->editing = true;
    }
    
    public function save()
    {
        $this->dispatch('block-updated', index: $this->index, data: $this->block['data']);
        $this->editing = false;
    }
    
    public function cancel()
    {
        $this->editing = false;
        $this->loadBlockMetadata();
    }
    
    public function remove()
    {
        $this->dispatch('block-removed', index: $this->index);
    }
    
    public function moveUp()
    {
        if ($this->index > 0) {
            $this->dispatch('block-moved', fromIndex: $this->index, toIndex: $this->index - 1);
        }
    }
    
    public function moveDown()
    {
        $this->dispatch('block-moved', fromIndex: $this->index, toIndex: $this->index + 1);
    }
    
    public function openMediaLibraryForField($fieldName)
    {
        $this->mediaFieldActive = $fieldName;
        $this->dispatch('open-media-library', field: $fieldName);
    }
    
    #[On('media-selected')]
    public function handleMediaSelected($url)
    {
        if ($this->mediaFieldActive && $this->editing) {
            $fieldPath = $this->mediaFieldActive;
            $keys = explode('.', $fieldPath);
            
            if (count($keys) === 1) {
                // Campo simples
                $this->block['data'][$keys[0]] = $url;
            } elseif (count($keys) === 3) {
                // Campo em repeater (ex: gallery.0.image)
                list($parent, $index, $field) = $keys;
                if (isset($this->block['data'][$parent][$index])) {
                    $this->block['data'][$parent][$index][$field] = $url;
                }
            }
            
            $this->mediaFieldActive = null;
            $this->dispatch('block-updated', index: $this->index, data: $this->block['data']);
        }
    }
    
    public function updated($property, $value)
    {
        if (str_starts_with($property, 'block.data.') && $this->editing) {
            $this->dispatch('block-updated', index: $this->index, data: $this->block['data']);
        }
    }
    
    public function addRepeaterItem($fieldName)
    {
        if (isset($this->blockSchema[$fieldName]['type']) && 
            $this->blockSchema[$fieldName]['type'] === 'repeater') {
            
            $newItem = [];
            foreach ($this->blockSchema[$fieldName]['fields'] as $subFieldName => $subField) {
                $newItem[$subFieldName] = $subField['default'] ?? null;
            }
            
            if (!isset($this->block['data'][$fieldName])) {
                $this->block['data'][$fieldName] = [];
            }
            
            $this->block['data'][$fieldName][] = $newItem;
            $this->dispatch('block-updated', index: $this->index, data: $this->block['data']);
        }
    }
    
    public function removeRepeaterItem($fieldName, $index)
    {
        if (isset($this->block['data'][$fieldName][$index])) {
            array_splice($this->block['data'][$fieldName], $index, 1);
            $this->dispatch('block-updated', index: $this->index, data: $this->block['data']);
        }
    }
    
    public function clearField($fieldName)
    {
        if (isset($this->block['data'][$fieldName])) {
            $this->block['data'][$fieldName] = null;
            $this->dispatch('block-updated', index: $this->index, data: $this->block['data']);
        }
    }
}