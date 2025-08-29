<?php

namespace Justino\PageBuilder\Http\Livewire;

use Livewire\Component;
use Justino\PageBuilder\Services\BlockManager;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class BlockEditor extends Component
{
    public $blockId;
    public $blockType;
    public $blockData = [];
    public $blockStyles = [];
    public $isEditing = false;
    public $validationErrors = [];
    public $hasError = false;
    public $errorMessage = '';
    
    protected $blockManager;
    protected $listeners = [
        'mediaSelected' => 'handleMediaSelected',
        'fieldUpdated' => 'handleFieldUpdate'
    ];
    
    public function mount($blockId, $blockType, $initialData = [], $initialStyles = [])
    {
        try {
            $this->validateInputs($blockId, $blockType, $initialData, $initialStyles);
            
            $this->blockId = $blockId;
            $this->blockType = $blockType;
            $this->blockData = $initialData;
            $this->blockStyles = $initialStyles;
            $this->blockManager = app(BlockManager::class);
            
            $this->validateBlockType();
            $this->fillWithDefaults();
            
        } catch (\Exception $e) {
            $this->handleError($e);
        }
    }
    
    protected function validateInputs($blockId, $blockType, $initialData, $initialStyles): void
    {
        if (!is_int($blockId) && !is_string($blockId)) {
            throw new InvalidArgumentException('Block ID must be integer or string');
        }
        
        if (!is_string($blockType) || empty($blockType)) {
            throw new InvalidArgumentException('Block type must be a non-empty string');
        }
        
        if (!is_array($initialData)) {
            throw new InvalidArgumentException('Initial data must be an array');
        }
        
        if (!is_array($initialStyles)) {
            throw new InvalidArgumentException('Initial styles must be an array');
        }
    }
    
    protected function validateBlockType(): void
    {
        if (!$this->blockManager->isValidBlockType($this->blockType)) {
            throw new InvalidArgumentException("Block type '{$this->blockType}' is not registered");
        }
    }
    
    protected function fillWithDefaults(): void
    {
        try {
            $defaults = $this->blockManager->getBlockDefaults($this->blockType);
            
            foreach ($defaults as $key => $defaultValue) {
                if (!isset($this->blockData[$key])) {
                    $this->blockData[$key] = $defaultValue;
                }
            }
        } catch (\Exception $e) {
            Log::warning("Failed to fill defaults for block {$this->blockType}", [
                'error' => $e->getMessage(),
                'block_id' => $this->blockId
            ]);
        }
    }
    
    protected function handleError(\Exception $e): void
    {
        $this->hasError = true;
        $this->errorMessage = 'Erro ao carregar bloco';
        
        Log::error('BlockEditor error: ' . $e->getMessage(), [
            'block_id' => $this->blockId,
            'block_type' => $this->blockType,
            'exception' => $e
        ]);
    }
    
    public function render()
    {
        if ($this->hasError) {
            return view('pagebuilder::livewire.block-editor-error', [
                'errorMessage' => $this->errorMessage,
                'blockId' => $this->blockId,
                'blockType' => $this->blockType
            ]);
        }
        
        try {
            $blockClass = $this->blockManager->getBlockClassName($this->blockType);
            $schema = $this->blockManager->getBlockSchema($this->blockType);
            
            return view('pagebuilder::livewire.block-editor', [
                'blockLabel' => $blockClass ? $blockClass::label() : $this->blockType,
                'blockIcon' => $blockClass ? $blockClass::icon() : '📦',
                'blockSchema' => $schema,
                'editorComponent' => $this->blockManager->getEditorComponent($this->blockType),
                'previewComponent' => $this->blockManager->getPreviewComponent($this->blockType)
            ]);
            
        } catch (\Exception $e) {
            Log::error('BlockEditor render error: ' . $e->getMessage(), [
                'block_id' => $this->blockId,
                'block_type' => $this->blockType
            ]);
            
            return view('pagebuilder::livewire.block-editor-error', [
                'errorMessage' => 'Erro ao renderizar bloco',
                'blockId' => $this->blockId,
                'blockType' => $this->blockType
            ]);
        }
    }
    
    public function startEditing(): void
    {
        if ($this->hasError) return;
        
        $this->isEditing = true;
        $this->validationErrors = [];
        $this->dispatch('blockEditorOpened', blockId: $this->blockId);
    }
    
    public function save(): void
    {
        if ($this->hasError) return;
        
        if ($this->validateBlockData()) {
            $this->dispatch('blockUpdated', [
                'id' => $this->blockId,
                'type' => $this->blockType,
                'data' => $this->blockData,
                'styles' => $this->blockStyles
            ]);
            
            $this->isEditing = false;
            $this->dispatch('notify', message: 'Bloco salvo com sucesso!', type: 'success');
        }
    }
    
    public function cancel(): void
    {
        if ($this->hasError) return;
        
        $this->isEditing = false;
        $this->validationErrors = [];
        $this->dispatch('blockEditCancelled', blockId: $this->blockId);
    }
    
    public function remove(): void
    {
        $this->dispatch('blockRemoved', blockId: $this->blockId);
    }
    
    protected function validateBlockData(): bool
    {
        try {
            $schema = $this->blockManager->getBlockSchema($this->blockType);
            $rules = $this->buildValidationRules($schema);
            
            $validator = Validator::make(
                ['data' => $this->blockData],
                ['data' => $rules]
            );
            
            if ($validator->fails()) {
                $this->validationErrors = $validator->errors()->get('data');
                $this->dispatch('notify', message: 'Corrija os erros de validação.', type: 'error');
                return false;
            }
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Block validation error: ' . $e->getMessage(), [
                'block_id' => $this->blockId,
                'block_type' => $this->blockType
            ]);
            
            $this->validationErrors = ['Erro na validação do bloco'];
            return false;
        }
    }
    
    protected function buildValidationRules(array $schema): array
    {
        $rules = [];
        
        foreach ($schema as $fieldName => $fieldConfig) {
            $fieldRules = [];
            
            if (isset($fieldConfig['required']) && $fieldConfig['required']) {
                $fieldRules[] = 'required';
            }
            
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
        if ($this->hasError) return;
        
        try {
            $this->updateNestedField($fieldPath, $url);
            $this->dispatch('fieldUpdated', fieldPath: $fieldPath, value: $url);
        } catch (\Exception $e) {
            Log::error('Media selection error: ' . $e->getMessage(), [
                'block_id' => $this->blockId,
                'field_path' => $fieldPath
            ]);
        }
    }
    
    public function handleFieldUpdate(string $fieldPath, $value): void
    {
        if ($this->hasError) return;
        
        try {
            $this->updateNestedField($fieldPath, $value);
            $this->dispatch('blockDataUpdated', blockId: $this->blockId, fieldPath: $fieldPath, value: $value);
        } catch (\Exception $e) {
            Log::error('Field update error: ' . $e->getMessage(), [
                'block_id' => $this->blockId,
                'field_path' => $fieldPath
            ]);
        }
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
        if ($this->hasError) return;
        
        try {
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
                $this->dispatch('repeaterItemAdded', fieldName: $fieldName, index: count($this->blockData[$fieldName]) - 1);
            }
        } catch (\Exception $e) {
            Log::error('Add repeater item error: ' . $e->getMessage(), [
                'block_id' => $this->blockId,
                'field_name' => $fieldName
            ]);
        }
    }
    
    public function removeRepeaterItem(string $fieldName, int $index): void
    {
        if ($this->hasError) return;
        
        try {
            if (isset($this->blockData[$fieldName][$index])) {
                array_splice($this->blockData[$fieldName], $index, 1);
                $this->dispatch('repeaterItemRemoved', fieldName: $fieldName, index: $index);
            }
        } catch (\Exception $e) {
            Log::error('Remove repeater item error: ' . $e->getMessage(), [
                'block_id' => $this->blockId,
                'field_name' => $fieldName,
                'index' => $index
            ]);
        }
    }
    
    public function moveRepeaterItem(string $fieldName, int $fromIndex, int $toIndex): void
    {
        if ($this->hasError) return;
        
        try {
            if (isset($this->blockData[$fieldName][$fromIndex]) && isset($this->blockData[$fieldName][$toIndex])) {
                $item = $this->blockData[$fieldName][$fromIndex];
                array_splice($this->blockData[$fieldName], $fromIndex, 1);
                array_splice($this->blockData[$fieldName], $toIndex, 0, [$item]);
                $this->dispatch('repeaterItemMoved', fieldName: $fieldName, fromIndex: $fromIndex, toIndex: $toIndex);
            }
        } catch (\Exception $e) {
            Log::error('Move repeater item error: ' . $e->getMessage(), [
                'block_id' => $this->blockId,
                'field_name' => $fieldName,
                'from_index' => $fromIndex,
                'to_index' => $toIndex
            ]);
        }
    }
    
    public function updated($property, $value): void
    {
        if ($this->hasError) return;
        
        try {
            if (str_starts_with($property, 'blockData.') || str_starts_with($property, 'blockStyles.')) {
                $fieldPath = str_replace(['blockData.', 'blockStyles.'], '', $property);
                $this->dispatch('blockDataChanged', blockId: $this->blockId, fieldPath: $fieldPath, value: $value);
            }
        } catch (\Exception $e) {
            Log::error('Property update error: ' . $e->getMessage(), [
                'block_id' => $this->blockId,
                'property' => $property
            ]);
        }
    }
    
    public function getBlockInfoProperty()
    {
        if ($this->hasError) {
            return [
                'type' => $this->blockType,
                'has_error' => true,
                'error_message' => $this->errorMessage
            ];
        }
        
        try {
            $blockClass = $this->blockManager->getBlockClassName($this->blockType);
            
            return [
                'type' => $this->blockType,
                'label' => $blockClass ? $blockClass::label() : 'Unknown',
                'icon' => $blockClass ? $blockClass::icon() : '📦',
                'has_error' => false
            ];
        } catch (\Exception $e) {
            return [
                'type' => $this->blockType,
                'has_error' => true,
                'error_message' => 'Erro ao obter informações do bloco'
            ];
        }
    }
}