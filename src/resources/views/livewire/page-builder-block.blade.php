<div class="page-builder-block relative border rounded-lg p-4 {{ $isSelected ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}"
     x-data="{ activeField: null }">
    <!-- Block Header -->
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center">
            <span class="text-2xl mr-2">{{ $blockIcon }}</span>
            <span class="font-medium">{{ $blockLabel }}</span>
        </div>
        
        <div class="flex space-x-2">
            <button wire:click="moveUp" class="p-1 text-gray-500 hover:text-gray-700" title="Move Up">
                ↑
            </button>
            <button wire:click="moveDown" class="p-1 text-gray-500 hover:text-gray-700" title="Move Down">
                ↓
            </button>
            <button wire:click="duplicate" class="p-1 text-green-500 hover:text-green-700" title="Duplicate">
                ⎘
            </button>
            <button wire:click="edit" class="p-1 text-blue-500 hover:text-blue-700" title="Edit">
                ✏️
            </button>
            <button wire:click="remove" class="p-1 text-red-500 hover:text-red-700" title="Delete">
                🗑️
            </button>
        </div>
    </div>
    
    <!-- Block Content -->
    @if($editing)
        <div class="space-y-4">
            @foreach($blockSchema as $fieldName => $field)
                <div class="block-field">
                    <label class="block text-sm font-medium mb-1">{{ $field['label'] }}</label>
                    
                    @if($field['type'] === 'text')
                        <input 
                            type="text" 
                            wire:model="block.data.{{ $fieldName }}"
                            class="w-full p-2 border rounded"
                            placeholder="{{ $field['default'] ?? '' }}"
                        >
                    
                    @elseif($field['type'] === 'textarea')
                        <textarea 
                            wire:model="block.data.{{ $fieldName }}"
                            class="w-full p-2 border rounded"
                            rows="3"
                            placeholder="{{ $field['default'] ?? '' }}"
                        ></textarea>
                    
                    @elseif($field['type'] === 'richtext')
                        <textarea 
                            wire:model="block.data.{{ $fieldName }}"
                            class="w-full p-2 border rounded h-32"
                            placeholder="{{ $field['default'] ?? '' }}"
                        ></textarea>
                    
                    @elseif($field['type'] === 'number')
                        <input 
                            type="number" 
                            wire:model="block.data.{{ $fieldName }}"
                            class="w-full p-2 border rounded"
                            value="{{ $field['default'] ?? '' }}"
                        >
                    
                    @elseif($field['type'] === 'color')
                        <div class="flex items-center space-x-2">
                            <input 
                                type="color" 
                                wire:model="block.data.{{ $fieldName }}"
                                class="w-12 h-12 p-1 border rounded"
                                value="{{ $field['default'] ?? '#000000' }}"
                            >
                            <input 
                                type="text" 
                                wire:model="block.data.{{ $fieldName }}"
                                class="flex-1 p-2 border rounded"
                                placeholder="{{ $field['default'] ?? '#000000' }}"
                            >
                        </div>
                    
                    @elseif($field['type'] === 'select')
                        <select 
                            wire:model="block.data.{{ $fieldName }}"
                            class="w-full p-2 border rounded">
                            @foreach($field['options'] as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    
                    @elseif($field['type'] === 'media')
                        <div class="flex items-center space-x-2">
                            <input 
                                type="text" 
                                wire:model="block.data.{{ $fieldName }}"
                                class="flex-1 p-2 border rounded"
                                placeholder="Image URL"
                            >
                            <button 
                                wire:click="openMediaLibraryForField('{{ $fieldName }}')"
                                class="p-2 bg-gray-200 rounded hover:bg-gray-300"
                                title="Select Media"
                            >
                                📁
                            </button>
                            @if($block['data'][$fieldName] ?? false)
                                <button 
                                    wire:click="clearField('{{ $fieldName }}')"
                                    class="p-2 bg-red-200 rounded hover:bg-red-300"
                                    title="Clear"
                                >
                                    ✕
                                </button>
                            @endif
                        </div>
                        
                        @if($block['data'][$fieldName] ?? false)
                            <div class="mt-2">
                                <img 
                                    src="{{ $block['data'][$fieldName] }}" 
                                    alt="Preview" 
                                    class="max-h-32 rounded mx-auto"
                                >
                            </div>
                        @endif
                    
                    @elseif($field['type'] === 'repeater')
                        <div class="repeater-field space-y-3">
                            @foreach($block['data'][$fieldName] ?? [] as $itemIndex => $item)
                                <div class="repeater-item border rounded p-3 bg-gray-50">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-medium">Item {{ $itemIndex + 1 }}</span>
                                        <div class="flex space-x-1">
                                            <button 
                                                wire:click="moveRepeaterItemUp('{{ $fieldName }}', {{ $itemIndex }})"
                                                class="p-1 text-gray-500 hover:text-gray-700"
                                                title="Move Up"
                                            >
                                                ↑
                                            </button>
                                            <button 
                                                wire:click="moveRepeaterItemDown('{{ $fieldName }}', {{ $itemIndex }})"
                                                class="p-1 text-gray-500 hover:text-gray-700"
                                                title="Move Down"
                                            >
                                                ↓
                                            </button>
                                            <button 
                                                wire:click="removeRepeaterItem('{{ $fieldName }}', {{ $itemIndex }})"
                                                class="p-1 text-red-500 hover:text-red-700"
                                                title="Remove"
                                            >
                                                🗑️
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        @foreach($field['fields'] as $subFieldName => $subField)
                                            <div>
                                                <label class="block text-xs font-medium mb-1">
                                                    {{ $subField['label'] }}
                                                </label>
                                                
                                                @if($subField['type'] === 'text')
                                                    <input 
                                                        type="text" 
                                                        wire:model="block.data.{{ $fieldName }}.{{ $itemIndex }}.{{ $subFieldName }}"
                                                        class="w-full p-2 border rounded text-sm"
                                                        placeholder="{{ $subField['default'] ?? '' }}"
                                                    >
                                                
                                                @elseif($subField['type'] === 'media')
                                                    <div class="flex items-center space-x-2">
                                                        <input 
                                                            type="text" 
                                                            wire:model="block.data.{{ $fieldName }}.{{ $itemIndex }}.{{ $subFieldName }}"
                                                            class="flex-1 p-2 border rounded text-sm"
                                                            placeholder="Image URL"
                                                        >
                                                        <button 
                                                            wire:click="openMediaLibraryForField('{{ $fieldName }}.{{ $itemIndex }}.{{ $subFieldName }}')"
                                                            class="p-1 bg-gray-200 rounded hover:bg-gray-300"
                                                            title="Select Media"
                                                        >
                                                            📁
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                            
                            <button 
                                wire:click="addRepeaterItem('{{ $fieldName }}')"
                                class="w-full p-2 bg-green-100 text-green-800 rounded hover:bg-green-200 text-sm"
                            >
                                + Add Item
                            </button>
                        </div>
                    @endif
                    
                    @if($field['description'] ?? false)
                        <p class="text-xs text-gray-500 mt-1">{{ $field['description'] }}</p>
                    @endif
                </div>
            @endforeach
            
            <div class="flex justify-end space-x-2 pt-4 border-t">
                <button 
                    wire:click="cancelEdit"
                    class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300"
                >
                    {{ __('pagebuilder::messages.cancel') }}
                </button>
                <button 
                    wire:click="save"
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                >
                    {{ __('pagebuilder::messages.save') }}
                </button>
            </div>
        </div>
    @else
        <!-- Block Preview -->
        <div class="prose max-w-none cursor-pointer" wire:click="edit">
            @if($block['type'] === 'hero')
                <div class="bg-gray-200 p-8 rounded text-center">
                    <h2 class="text-2xl font-bold">{{ $block['data']['title'] ?? 'Hero Title' }}</h2>
                    <p class="text-gray-600">{{ $block['data']['subtitle'] ?? 'Hero subtitle' }}</p>
                    @if($block['data']['cta_text'] ?? false)
                        <button class="mt-4 px-6 py-2 bg-blue-500 text-white rounded">
                            {{ $block['data']['cta_text'] }}
                        </button>
                    @endif
                </div>
            
            @elseif($block['type'] === 'text')
                <div class="p-4 bg-white rounded">
                    {!! $block['data']['content'] ?? '<p>Text content</p>' !!}
                </div>
            
            @elseif($block['type'] === 'cta')
                <div class="p-6 bg-blue-100 rounded text-center">
                    <h3 class="text-xl font-bold">{{ $block['data']['title'] ?? 'Call to Action' }}</h3>
                    <p class="text-gray-700">{{ $block['data']['description'] ?? 'Description' }}</p>
                    <button class="mt-4 px-6 py-2 bg-blue-500 text-white rounded">
                        {{ $block['data']['button_text'] ?? 'Click Me' }}
                    </button>
                </div>
            
            @else
                <div class="p-4 bg-gray-100 rounded text-center text-gray-500">
                    <div class="text-2xl mb-2">{{ $blockIcon }}</div>
                    <p>{{ $blockLabel }} Preview</p>
                    <p class="text-xs mt-2">Click to edit</p>
                </div>
            @endif
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Listen for block events
    Livewire.on('block-saved', (data) => {
        toastr.success(data.message);
    });
    
    Livewire.on('block-removed', (data) => {
        toastr.info(data.message);
    });
    
    Livewire.on('media-selected', (data) => {
        toastr.success(data.message);
    });
});
</script>
@endpush