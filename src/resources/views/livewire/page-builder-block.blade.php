<div class="page-builder-block relative border rounded-lg p-4 {{ $isSelected ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-200' : 'border-gray-300' }} transition-all duration-200 group"
     x-data="{ 
        editing: {{ $editing ? 'true' : 'false' }},
        showQuickActions: false
     }"
     wire:click="selectBlock({{ $index }})"
     @click.away="showQuickActions = false">
    
    <!-- Quick Actions Toolbar -->
    <div class="absolute -top-3 -right-2 flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
         x-show="!editing && !showQuickActions"
         x-transition>
        <button wire:click="moveUp" 
                class="p-1 bg-white border rounded shadow-sm text-gray-600 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                title="Move Up">
            ↑
        </button>
        <button wire:click="moveDown" 
                class="p-1 bg-white border rounded shadow-sm text-gray-600 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                title="Move Down">
            ↓
        </button>
        <button wire:click="edit" 
                class="p-1 bg-white border rounded shadow-sm text-gray-600 hover:text-green-600 hover:bg-green-50 transition-colors"
                title="Edit Block">
            ✏️
        </button>
        <button wire:click="remove" 
                class="p-1 bg-white border rounded shadow-sm text-gray-600 hover:text-red-600 hover:bg-red-50 transition-colors"
                title="Delete Block"
                onclick="return confirm('Are you sure you want to delete this block?')">
            🗑️
        </button>
        <button @click="showQuickActions = true" 
                class="p-1 bg-white border rounded shadow-sm text-gray-600 hover:text-purple-600 hover:bg-purple-50 transition-colors"
                title="Quick Style">
            🎨
        </button>
    </div>

    <!-- Quick Style Actions -->
    <div class="absolute -top-10 right-0 bg-white border rounded-lg shadow-lg p-2 space-y-2 z-10"
         x-show="showQuickActions"
         x-transition
         @click.outside="showQuickActions = false">
        <div class="text-xs font-medium text-gray-700 mb-2">Quick Style</div>
        <div class="grid grid-cols-2 gap-1">
            <button wire:click="$dispatch('open-style-editor', { type: '{{ $block['type'] }}', index: {{ $index }} })"
                    class="p-1 text-xs bg-blue-50 text-blue-700 rounded hover:bg-blue-100 transition-colors">
                🎨 Style
            </button>
            <button wire:click="duplicate"
                    class="p-1 text-xs bg-green-50 text-green-700 rounded hover:bg-green-100 transition-colors">
                ⎘ Duplicate
            </button>
        </div>
    </div>

    <!-- Block Header -->
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center">
            <span class="text-2xl mr-2">{{ $blockIcon }}</span>
            <span class="font-medium text-gray-800">{{ $blockLabel }}</span>
        </div>
        
        <div class="flex items-center space-x-1">
            <!-- Status indicator -->
            <div class="w-2 h-2 rounded-full {{ $editing ? 'bg-green-400' : 'bg-gray-300' }}"></div>
        </div>
    </div>
    
    <!-- Block Content -->
    <template x-if="editing">
        <div class="space-y-4" @click.stop>
            @foreach($blockSchema as $fieldName => $field)
                <div class="block-field">
                    <label class="block text-sm font-medium mb-2 text-gray-700">
                        {{ $field['label'] }}
                        @if(isset($field['required']) && $field['required'])
                            <span class="text-red-500">*</span>
                        @endif
                    </label>
                    
                    @if($field['type'] === 'text')
                        <input 
                            type="text" 
                            wire:model="block.data.{{ $fieldName }}"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                            placeholder="{{ $field['placeholder'] ?? $field['label'] }}"
                            @if(isset($field['required']) && $field['required']) required @endif
                        >
                    
                    @elseif($field['type'] === 'textarea')
                        <textarea 
                            wire:model="block.data.{{ $fieldName }}"
                            rows="{{ $field['rows'] ?? 4 }}"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                            placeholder="{{ $field['placeholder'] ?? $field['label'] }}"
                        ></textarea>
                    
                    @elseif($field['type'] === 'richtext')
                        <div class="border border-gray-300 rounded-lg overflow-hidden">
                            <textarea 
                                wire:model="block.data.{{ $fieldName }}"
                                rows="6"
                                class="w-full p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="{{ $field['placeholder'] ?? $field['label'] }}"
                            ></textarea>
                        </div>
                    
                    @elseif($field['type'] === 'number')
                        <input 
                            type="number" 
                            wire:model="block.data.{{ $fieldName }}"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            min="{{ $field['min'] ?? 0 }}"
                            max="{{ $field['max'] ?? 100 }}"
                            step="{{ $field['step'] ?? 1 }}"
                        >
                    
                    @elseif($field['type'] === 'color')
                        <div class="flex items-center space-x-3">
                            <input 
                                type="color" 
                                wire:model="block.data.{{ $fieldName }}"
                                class="w-12 h-12 p-1 border border-gray-300 rounded cursor-pointer"
                            >
                            <input 
                                type="text" 
                                wire:model="block.data.{{ $fieldName }}"
                                class="flex-1 p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="#000000"
                            >
                        </div>
                    
                    @elseif($field['type'] === 'select')
                        <select 
                            wire:model="block.data.{{ $fieldName }}"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">{{ __('pagebuilder::messages.select_option') }}</option>
                            @foreach($field['options'] as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    
                    @elseif($field['type'] === 'media')
                        <div class="space-y-2">
                            <div class="flex items-center space-x-2">
                                <input 
                                    type="text" 
                                    wire:model="block.data.{{ $fieldName }}"
                                    class="flex-1 p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="{{ $field['placeholder'] ?? 'Enter image URL or click to select' }}"
                                >
                                <button 
                                    wire:click="$dispatch('open-media-library', { field: '{{ $fieldName }}' })"
                                    class="px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors"
                                    type="button"
                                >
                                    📁
                                </button>
                                @if($block['data'][$fieldName] ?? false)
                                    <button 
                                        wire:click="clearField('{{ $fieldName }}')"
                                        class="px-4 py-3 bg-red-100 border border-red-300 rounded-lg hover:bg-red-200 transition-colors"
                                        type="button"
                                    >
                                        ✕
                                    </button>
                                @endif
                            </div>
                            
                            @if($block['data'][$fieldName] ?? false)
                                <div class="mt-2 p-2 border border-gray-200 rounded-lg">
                                    <img 
                                        src="{{ $block['data'][$fieldName] }}" 
                                        alt="Preview" 
                                        class="max-h-32 rounded mx-auto"
                                        onerror="this.style.display='none'"
                                    >
                                    <p class="text-xs text-center text-gray-500 mt-2 truncate">
                                        {{ $block['data'][$fieldName] }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    
                    @elseif($field['type'] === 'repeater')
                        <div class="repeater-field space-y-3 border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <div class="flex justify-between items-center mb-3">
                                <span class="font-medium text-gray-700">{{ $field['label'] }}</span>
                                <button 
                                    wire:click="addRepeaterItem('{{ $fieldName }}')"
                                    class="px-3 py-1 bg-green-500 text-white text-sm rounded-lg hover:bg-green-600 transition-colors"
                                    type="button"
                                >
                                    + {{ __('pagebuilder::messages.add_item') }}
                                </button>
                            </div>
                            
                            @foreach($block['data'][$fieldName] ?? [] as $itemIndex => $item)
                                <div class="repeater-item border border-gray-300 rounded-lg p-4 bg-white">
                                    <div class="flex justify-between items-center mb-3">
                                        <span class="font-medium text-sm text-gray-600">
                                            {{ __('pagebuilder::messages.item') }} {{ $itemIndex + 1 }}
                                        </span>
                                        <div class="flex space-x-1">
                                            <button 
                                                wire:click="moveRepeaterItemUp('{{ $fieldName }}', {{ $itemIndex }})"
                                                class="p-1 text-gray-500 hover:text-blue-600 transition-colors"
                                                type="button"
                                                title="Move Up"
                                            >
                                                ↑
                                            </button>
                                            <button 
                                                wire:click="moveRepeaterItemDown('{{ $fieldName }}', {{ $itemIndex }})"
                                                class="p-1 text-gray-500 hover:text-blue-600 transition-colors"
                                                type="button"
                                                title="Move Down"
                                            >
                                                ↓
                                            </button>
                                            <button 
                                                wire:click="removeRepeaterItem('{{ $fieldName }}', {{ $itemIndex }})"
                                                class="p-1 text-gray-500 hover:text-red-600 transition-colors"
                                                type="button"
                                                title="Remove"
                                            >
                                                🗑️
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach($field['fields'] as $subFieldName => $subField)
                                            <div>
                                                <label class="block text-xs font-medium mb-1 text-gray-600">
                                                    {{ $subField['label'] }}
                                                </label>
                                                
                                                @if($subField['type'] === 'text')
                                                    <input 
                                                        type="text" 
                                                        wire:model="block.data.{{ $fieldName }}.{{ $itemIndex }}.{{ $subFieldName }}"
                                                        class="w-full p-2 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500"
                                                        placeholder="{{ $subField['placeholder'] ?? $subField['label'] }}"
                                                    >
                                                
                                                @elseif($subField['type'] === 'media')
                                                    <div class="flex items-center space-x-2">
                                                        <input 
                                                            type="text" 
                                                            wire:model="block.data.{{ $fieldName }}.{{ $itemIndex }}.{{ $subFieldName }}"
                                                            class="flex-1 p-2 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500"
                                                            placeholder="Image URL"
                                                        >
                                                        <button 
                                                            wire:click="$dispatch('open-media-library', { field: '{{ $fieldName }}.{{ $itemIndex }}.{{ $subFieldName }}' })"
                                                            class="p-2 bg-gray-100 border border-gray-300 rounded hover:bg-gray-200 transition-colors"
                                                            type="button"
                                                        >
                                                            📁
                                                        </button>
                                                    </div>
                                                    
                                                    @if($block['data'][$fieldName][$itemIndex][$subFieldName] ?? false)
                                                        <div class="mt-1">
                                                            <img 
                                                                src="{{ $block['data'][$fieldName][$itemIndex][$subFieldName] }}" 
                                                                alt="Preview" 
                                                                class="max-h-16 rounded mx-auto"
                                                                onerror="this.style.display='none'"
                                                            >
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                            
                            @if(empty($block['data'][$fieldName] ?? []))
                                <div class="text-center py-4 text-gray-400">
                                    <div class="text-2xl mb-2">📝</div>
                                    <p class="text-sm">{{ __('pagebuilder::messages.no_items_added') }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                    
                    @if($field['description'] ?? false)
                        <p class="text-xs text-gray-500 mt-1">{{ $field['description'] }}</p>
                    @endif
                    
                    @error('block.data.' . $fieldName)
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach
            
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <button 
                    @click="editing = false; $wire.cancel()"
                    class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors"
                    type="button"
                >
                    {{ __('pagebuilder::messages.cancel') }}
                </button>
                <button 
                    wire:click="save"
                    class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors"
                    type="button"
                >
                    {{ __('pagebuilder::messages.save_changes') }}
                </button>
            </div>
        </div>
    </template>
    
    <template x-if="!editing">
        <div class="cursor-pointer group-hover:opacity-90 transition-opacity" @click="if(!showQuickActions) $wire.edit()">
            <!-- Dynamic Block Preview -->
            @if($block['type'] === 'text')
                <div class="prose max-w-none p-4 bg-white rounded-lg border border-gray-200">
                    {!! $block['data']['content'] ?? '<p class="text-gray-400">' . __('pagebuilder::messages.click_to_edit_text') . '</p>' !!}
                </div>
            
            @elseif($block['type'] === 'hero')
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-8 rounded-lg text-center border border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">
                        {{ $block['data']['title'] ?? __('pagebuilder::messages.hero_title') }}
                    </h2>
                    <p class="text-gray-600 mb-6">
                        {{ $block['data']['subtitle'] ?? __('pagebuilder::messages.hero_subtitle') }}
                    </p>
                    @if($block['data']['cta_text'] ?? false)
                        <button class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                            {{ $block['data']['cta_text'] }}
                        </button>
                    @else
                        <div class="px-6 py-2 bg-gray-200 text-gray-500 rounded-lg">
                            {{ __('pagebuilder::messages.add_button_text') }}
                        </div>
                    @endif
                </div>
            
            @elseif($block['type'] === 'cta')
                <div class="bg-blue-50 p-6 rounded-lg border border-blue-200 text-center">
                    <h3 class="text-xl font-semibold text-blue-800 mb-2">
                        {{ $block['data']['title'] ?? __('pagebuilder::messages.cta_title') }}
                    </h3>
                    <p class="text-blue-600 mb-4">
                        {{ $block['data']['description'] ?? __('pagebuilder::messages.cta_description') }}
                    </p>
                    <button class="px-5 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                        {{ $block['data']['button_text'] ?? __('pagebuilder::messages.learn_more') }}
                    </button>
                </div>
            
            @elseif($block['type'] === 'cards')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    @for($i = 0; $i < min(2, count($block['data']['cards'] ?? [])); $i++)
                        <div class="bg-white p-4 rounded-lg border border-gray-200">
                            <div class="text-2xl mb-2">{{ $block['data']['cards'][$i]['icon'] ?? '⭐' }}</div>
                            <h4 class="font-semibold text-gray-800 mb-1">
                                {{ $block['data']['cards'][$i]['title'] ?? __('pagebuilder::messages.card_title') }}
                            </h4>
                            <p class="text-sm text-gray-600">
                                {{ $block['data']['cards'][$i]['description'] ?? __('pagebuilder::messages.card_description') }}
                            </p>
                        </div>
                    @endfor
                    @if(empty($block['data']['cards'] ?? []))
                        <div class="text-center col-span-2 py-8 text-gray-400">
                            <div class="text-2xl mb-2">🃏</div>
                            <p>{{ __('pagebuilder::messages.add_cards_content') }}</p>
                        </div>
                    @endif
                </div>
            
            @else
                <!-- Default Block Preview -->
                <div class="text-center p-6 bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg border border-gray-200">
                    <div class="text-3xl mb-3">{{ $blockIcon }}</div>
                    <h3 class="font-semibold text-gray-700 mb-1">{{ $blockLabel }}</h3>
                    <p class="text-sm text-gray-500">{{ __('pagebuilder::messages.click_to_configure') }}</p>
                </div>
            @endif
            
            <!-- Block Type Badge -->
            <div class="absolute bottom-2 right-2">
                <span class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded-full">
                    {{ $block['type'] }}
                </span>
            </div>
        </div>
    </template>
</div>

@push('styles')
<style>
    .page-builder-block {
        transition: all 0.2s ease-in-out;
    }
    
    .page-builder-block:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    
    .repeater-item {
        transition: all 0.2s ease;
    }
    
    .repeater-item:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    /* Smooth transitions for all interactive elements */
    button, input, select, textarea {
        transition: all 0.2s ease;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        // Auto-save quando o usuário para de digitar
        let saveTimeout;
        
        Livewire.hook('element.updating', (el, component) => {
            if (component.name === 'page-builder-block' && component.$wire.editing) {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(() => {
                    component.$wire.save();
                }, 1000);
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && Livewire.get('editing')) {
                Livewire.get('editing') = false;
            }
        });
    });
</script>
@endpush