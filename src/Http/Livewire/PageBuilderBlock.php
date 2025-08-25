<?php

namespace Meu\PageBuilder\Http\Livewire;

use Livewire\Component;
use Meu\PageBuilder\Services\BlockManager;
use Meu\PageBuilder\Helpers\Translator;

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
    
    protected $listeners = [
        'mediaSelected' => 'handleMediaSelected',
        'openMediaLibrary' => 'openMediaLibraryForField'
    ];
    
    public function mount($index, $block, $isSelected = false)
    {
        $this->index = $index;
        $this->block = $block;
        $this->isSelected = $isSelected;
        
        // Carregar metadata do bloco
        $this->loadBlockMetadata();
    }
    
    public function loadBlockMetadata()
    {
        $blockManager = app(BlockManager::class);
        $blockClass = $blockManager->getBlockClass($this->block['type']);
        
        if ($blockClass) {
            $this->blockLabel = $blockClass::label();
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
    
    public function edit()
    {
        $this->editing = true;
    }
    
    public function save()
    {
        $this->emit('blockUpdated', $this->index, $this->block['data']);
        $this->editing = false;
        $this->dispatchBrowserEvent('block-saved', [
            'message' => Translator::trans('block_updated')
        ]);
    }
    
    public function cancelEdit()
    {
        $this->editing = false;
        // Recarregar dados originais se necessário
        $this->loadBlockMetadata();
    }
    
    public function remove()
    {
        $this->emit('blockRemoved', $this->index);
        $this->dispatchBrowserEvent('block-removed', [
            'message' => Translator::trans('block_removed')
        ]);
    }
    
    public function moveUp()
    {
        if ($this->index > 0) {
            $this->emit('blockMoved', $this->index, $this->index - 1);
        }
    }
    
    public function moveDown()
    {
        $this->emit('blockMoved', $this->index, $this->index + 1);
    }
    
    public function duplicate()
    {
        $this->emit('blockDuplicated', $this->index, $this->block);
    }
    
    public function handleMediaSelected($url)
    {
        if ($this->mediaFieldActive) {
            $this->block['data'][$this->mediaFieldActive] = $url;
            $this->mediaFieldActive = null;
            $this->emit('blockUpdated', $this->index, $this->block['data']);
            
            $this->dispatchBrowserEvent('media-selected', [
                'message' => Translator::trans('media_selected')
            ]);
        }
    }
    
    public function openMediaLibraryForField($fieldName = null)
    {
        $this->mediaFieldActive = $fieldName;
        $this->emit('openMediaLibrary');
    }
    
    public function clearField($fieldName)
    {
        if (isset($this->block['data'][$fieldName])) {
            $this->block['data'][$fieldName] = null;
            $this->emit('blockUpdated', $this->index, $this->block['data']);
        }
    }
    
    public function updateField($fieldName, $value)
    {
        $this->block['data'][$fieldName] = $value;
        $this->emit('blockUpdated', $this->index, $this->block['data']);
    }
    
    public function updateRepeaterField($fieldName, $index, $subFieldName, $value)
    {
        if (isset($this->block['data'][$fieldName][$index][$subFieldName])) {
            $this->block['data'][$fieldName][$index][$subFieldName] = $value;
            $this->emit('blockUpdated', $this->index, $this->block['data']);
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
            
            $this->block['data'][$fieldName][] = $newItem;
            $this->emit('blockUpdated', $this->index, $this->block['data']);
        }
    }
    
    public function removeRepeaterItem($fieldName, $index)
    {
        if (isset($this->block['data'][$fieldName][$index])) {
            array_splice($this->block['data'][$fieldName], $index, 1);
            $this->emit('blockUpdated', $this->index, $this->block['data']);
        }
    }
    
    public function moveRepeaterItemUp($fieldName, $index)
    {
        if ($index > 0 && isset($this->block['data'][$fieldName][$index])) {
            $item = $this->block['data'][$fieldName][$index];
            array_splice($this->block['data'][$fieldName], $index, 1);
            array_splice($this->block['data'][$fieldName], $index - 1, 0, [$item]);
            $this->emit('blockUpdated', $this->index, $this->block['data']);
        }
    }
    
    public function moveRepeaterItemDown($fieldName, $index)
    {
        if (isset($this->block['data'][$fieldName][$index + 1])) {
            $item = $this->block['data'][$fieldName][$index];
            array_splice($this->block['data'][$fieldName], $index, 1);
            array_splice($this->block['data'][$fieldName], $index + 1, 0, [$item]);
            $this->emit('blockUpdated', $this->index, $this->block['data']);
        }
    }
    
    public function applyStyle($styleProperty, $value)
    {
        if (!isset($this->block['styles'])) {
            $this->block['styles'] = [];
        }
        
        $this->block['styles'][$styleProperty] = $value;
        $this->emit('blockStylesUpdated', $this->index, $this->block['styles']);
    }
    
    public function resetStyles()
    {
        $this->block['styles'] = [];
        $this->emit('blockStylesUpdated', $this->index, []);
    }
    
    public function updatedBlock($value, $key)
    {
        $keys = explode('.', $key);
        
        if (count($keys) >= 2 && $keys[0] === 'data') {
            $this->emit('blockUpdated', $this->index, $this->block['data']);
        }
    }
    
    public function showPreview()
    {
        $this->dispatchBrowserEvent('show-block-preview', [
            'blockType' => $this->block['type'],
            'blockData' => $this->block['data']
        ]);
    }
    
    public function exportBlock()
    {
        $blockData = [
            'type' => $this->block['type'],
            'data' => $this->block['data'],
            'styles' => $this->block['styles'] ?? []
        ];
        
        $this->dispatchBrowserEvent('export-block', [
            'data' => json_encode($blockData, JSON_PRETTY_PRINT),
            'filename' => $this->block['type'] . '-block.json'
        ]);
    }
    
    public function importBlock($jsonData)
    {
        try {
            $importedData = json_decode($jsonData, true);
            
            if (json_last_error() === JSON_ERROR_NONE && 
                isset($importedData['type']) && 
                $importedData['type'] === $this->block['type']) {
                
                $this->block['data'] = $importedData['data'] ?? [];
                $this->block['styles'] = $importedData['styles'] ?? [];
                
                $this->emit('blockUpdated', $this->index, $this->block['data']);
                $this->emit('blockStylesUpdated', $this->index, $this->block['styles']);
                
                $this->dispatchBrowserEvent('block-imported', [
                    'message' => Translator::trans('block_imported')
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('import-error', [
                'message' => Translator::trans('import_error')
            ]);
        }
    }
}