<div class="page-builder-block relative border rounded-lg p-4 {{ $isSelected ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
    <!-- Block Header -->
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center">
            <span class="text-2xl mr-2">{{ $block['icon'] ?? '📦' }}</span>
            <span class="font-medium">{{ $block['label'] ?? 'Block' }}</span>
        </div>
        
        <div class="flex space-x-2">
            <button wire:click="moveUp" class="p-1 text-gray-500 hover:text-gray-700">
                ↑
            </button>
            <button wire:click="moveDown" class="p-1 text-gray-500 hover:text-gray-700">
                ↓
            </button>
            <button wire:click="edit" class="p-1 text-blue-500 hover:text-blue-700">
                ✏️
            </button>
            <button wire:click="remove" class="p-1 text-red-500 hover:text-red-700">
                🗑️
            </button>
        </div>
    </div>
    
    <!-- Block Content -->
    @if($editing)
        <div class="space-y-4">
            @foreach($blockSchema as $fieldName => $field)
                <div>
                    <label class="block text-sm font-medium mb-1">{{ $field['label'] }}</label>
                    
                    @if($field['type'] === 'text')
                        <input 
                            type="text" 
                            wire:model="block.data.{{ $fieldName }}"
                            class="w-full p-2 border rounded"
                        >
                    
                    @elseif($field['type'] === 'richtext')
                        <textarea 
                            wire:model="block.data.{{ $fieldName }}"
                            class="w-full p-2 border rounded h-32"
                        ></textarea>
                    
                    @elseif($field['type'] === 'media')
                        <div class="flex items-center space-x-2">
                            <input 
                                type="text" 
                                wire:model="block.data.{{ $fieldName }}"
                                class="flex-1 p-2 border rounded"
                                placeholder="Image URL"
                            >
                            <button 
                                wire:click="$emit('openMediaLibrary')"
                                class="p-2 bg-gray-200 rounded hover:bg-gray-300"
                            >
                                📁
                            </button>
                        </div>
                        
                        @if($block['data'][$fieldName])
                            <div class="mt-2">
                                <img 
                                    src="{{ $block['data'][$fieldName] }}" 
                                    alt="Preview" 
                                    class="max-h-32 rounded"
                                >
                            </div>
                        @endif
                    @endif
                </div>
            @endforeach
            
            <div class="flex justify-end space-x-2">
                <button 
                    wire:click="save" 
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                >
                    Save
                </button>
                <button 
                    wire:click="$set('editing', false)" 
                    class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300"
                >
                    Cancel
                </button>
            </div>
        </div>
    @else
        <!-- Block Preview -->
        <div class="prose">
            @if($block['type'] === 'hero')
                <div class="bg-gray-200 p-8 rounded text-center">
                    <h2 class="text-2xl font-bold">{{ $block['data']['title'] ?? 'Hero Title' }}</h2>
                    <p class="text-gray-600">{{ $block['data']['subtitle'] ?? 'Hero subtitle' }}</p>
                    @if($block['data']['cta_text'])
                        <button class="mt-4 px-6 py-2 bg-blue-500 text-white rounded">
                            {{ $block['data']['cta_text'] }}
                        </button>
                    @endif
                </div>
            
            @elseif($block['type'] === 'text')
                <div class="p-4 bg-white rounded">
                    {!! $block['data']['content'] ?? '<p>Text content</p>' !!}
                </div>
            
            @else
                <div class="p-4 bg-gray-100 rounded text-center text-gray-500">
                    {{ $block['label'] }} Block Preview
                </div>
            @endif
        </div>
    @endif
</div>