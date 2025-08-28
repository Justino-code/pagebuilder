<?php

namespace Justino\PageBuilder\Http\Livewire;

use Livewire\Component;
use Justino\PageBuilder\Services\BlockManager;
use Justino\PageBuilder\DTOs\BlockData;
use Illuminate\Support\Facades\Validator;

class BlockEditor extends Component
{
    public $blockId;
    public $blockType;
    public $blockData = [];
    public $blockStyles = [];
    public $isEditing = false;
    public $validationErrors = [];
    
    protected $blockManager;
    protected $listeners = [
        'mediaSelected' => 'handleMediaSelected',
        'fieldUpdated' => 'handleFieldUpdate'
    ];
    
    public function mount($blockId, $blockType, $initialData = [], $initialStyles = [])
    {
        $this->blockId = $blockId;
        $this->blockType = $blockType;
        $this->blockData = $initialData;
        $this->blockStyles = $initialStyles;
        $this->blockManager = app(BlockManager::class);
        
        // Preencher com valores padrão se necessário
        $this->fillWithDefaults();
    }
    
    protected function fillWithDefaults(): void
    {
        $defaults = $this->blockManager->getBlockDefaults($this->blockType);
        
        foreach ($defaults as $key => $defaultValue) {
            if (!isset($this->blockData[$key])) {
                $this->blockData[$key] = $defaultValue;
            }
        }
    }
    
    public function render()
    {
        $blockClass = $this->blockManager->getBlockClass($this->blockType);
        $schema = $this->blockManager->getBlockSchema($this->blockType);
        
        return view('pagebuilder::livewire.block-editor', [
            'blockLabel' => $blockClass ? $blockClass::label() : $this->blockType,
            'blockIcon' => $blockClass ? $blockClass::icon() : '📦',
            'blockSchema' => $schema,
            'editorComponent' => $this->blockManager->getEditorComponent($this->blockType),
            'previewComponent' => $this->blockManager->getPreviewComponent($this->blockType)
        ]);
    }
    
    public function startEditing(): void
    {
        $this->isEditing = true;
        $this->validationErrors = [];
        $this->emit('blockEditorOpened', $this->blockId);
    }
    
    public function save(): void
    {
        if ($this->validateBlockData()) {
            $this->emit('blockUpdated', [
                'id' => $this->blockId,
                'type' => $this->blockType,
                'data' => $this->blockData,
                'styles' => $this->blockStyles
            ]);
            
            $this->isEditing = false;
            $this->emit('notify', 'Block saved successfully!', 'success');
        }
    }
    
    public function cancel(): void
    {
        $this->isEditing = false;
        $this->validationErrors = [];
        $this->emit('blockEditCancelled', $this->blockId);
    }
    
    public function remove(): void
    {
        $this->emit('blockRemoved', $this->blockId);
    }
    
    protected function validateBlockData(): bool
    {
        $schema = $this->blockManager->getBlockSchema($this->blockType);
        $rules = $this->buildValidationRules($schema);
        
        $validator = Validator::make(
            ['data' => $this->blockData],
            ['data' => $rules]
        );
        
        if ($validator->fails()) {
            $this->validationErrors = $validator->errors()->get('data');
            $this->emit('notify', 'Please fix the validation errors.', 'error');
            return false;
        }
        
        return true;
    }
    
    protected function buildValidationRules(array $schema): array
    {
        $rules = [];
        
        foreach ($schema as $fieldName => $fieldConfig) {
            $fieldRules = [];
            
            if (isset($fieldConfig['required']) && $fieldConfig['required']) {
                $fieldRules[] = 'required';
            }
            
            // Adicionar regras baseadas no tipo do campo
            switch ($fieldConfig['type'] ?? 'text') {
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'url':
                    $fieldRules[] = 'url';
                    break;
                case 'number':
                    $fieldRules[] = 'numeric';
                    break;
            }
            
            if (!empty($fieldRules)) {
                $rules[$fieldName] = $fieldRules;
            }
        }
        
        return $rules;
    }
    
    public function handleMediaSelected(string $url, string $fieldPath): void
    {
        $this->updateNestedField($fieldPath, $url);
        $this->emit('fieldUpdated', $fieldPath, $url);
    }
    
    public function handleFieldUpdate(string $fieldPath, $value): void
    {
        $this->updateNestedField($fieldPath, $value);
        $this->emit('blockDataUpdated', $this->blockId, $fieldPath, $value);
    }
    
    protected function updateNestedField(string $path, $value): void
    {
        $keys = explode('.', $path);
        $data = &$this->blockData;
        
        foreach ($keys as $key) {
            if (!isset($data[$key]) || !is_array($data[$key])) {
                $data[$key] = [];
            }
            $data = &$data[$key];
        }
        
        $data = $value;
    }
    
    public function addRepeaterItem(string $fieldName): void
    {
        $schema = $this->blockManager->getBlockSchema($this->blockType);
        
        if (isset($schema[$fieldName]['type']) && $schema[$fieldName]['type'] === 'repeater') {
            $newItem = [];
            
            foreach ($schema[$fieldName]['fields'] as $subFieldName => $subFieldConfig) {
                $newItem[$subFieldName] = $subFieldConfig['default'] ?? null;
            }
            
            if (!isset($this->blockData[$fieldName])) {
                $this->blockData[$fieldName] = [];
            }
            
            $this->blockData[$fieldName][] = $newItem;
            $this->emit('repeaterItemAdded', $fieldName, count($this->blockData[$fieldName]) - 1);
        }
    }
    
    public function removeRepeaterItem(string $fieldName, int $index): void
    {
        if (isset($this->blockData[$fieldName][$index])) {
            array_splice($this->blockData[$fieldName], $index, 1);
            $this->emit('repeaterItemRemoved', $fieldName, $index);
        }
    }
    
    public function moveRepeaterItem(string $fieldName, int $fromIndex, int $toIndex): void
    {
        if (isset($this->blockData[$fieldName][$fromIndex]) && isset($this->blockData[$fieldName][$toIndex])) {
            $item = $this->blockData[$fieldName][$fromIndex];
            array_splice($this->blockData[$fieldName], $fromIndex, 1);
            array_splice($this->blockData[$fieldName], $toIndex, 0, [$item]);
            $this->emit('repeaterItemMoved', $fieldName, $fromIndex, $toIndex);
        }
    }
    
    public function updated($property, $value): void
    {
        if (str_starts_with($property, 'blockData.') || str_starts_with($property, 'blockStyles.')) {
            $fieldPath = str_replace(['blockData.', 'blockStyles.'], '', $property);
            $this->emit('blockDataChanged', $this->blockId, $fieldPath, $value);
        }
    }
}