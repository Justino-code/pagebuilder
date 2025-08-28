<div class="header-editor space-y-6">
    <!-- Template Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Template Name *</label>
            <input type="text" wire:model="block.data.name" 
                   class="w-full p-2 border border-gray-300 rounded-md" 
                   placeholder="Header name" required>
        </div>
        <div class="flex items-center">
            <input type="checkbox" 
                   wire:model="block.data.is_default"
                   id="is-default-header"
                   class="w-4 h-4 text-blue-600 border-gray-300 rounded">
            <label for="is-default-header" class="ml-2 text-sm text-gray-700">
                Set as default header
            </label>
        </div>
    </div>

    <!-- Logo Settings -->
    <div class="p-4 bg-gray-50 rounded-lg">
        <h4 class="font-medium text-gray-800 mb-3">Logo Settings</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Logo Type</label>
                <select wire:model="block.data.logo.type" class="w-full p-2 border border-gray-300 rounded-md">
                    <option value="text">Text</option>
                    <option value="image">Image</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Logo Link</label>
                <input type="text" wire:model="block.data.logo.link" 
                       class="w-full p-2 border border-gray-300 rounded-md" 
                       placeholder="/">
            </div>
        </div>

        <!-- Text Logo -->
        @if(($block['data']['logo']['type'] ?? 'text') === 'text')
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Logo Text *</label>
            <input type="text" wire:model="block.data.logo.text" 
                   class="w-full p-2 border border-gray-300 rounded-md" 
                   placeholder="My Website" required>
        </div>
        @else
        <!-- Image Logo -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Logo Image *</label>
            <div class="flex space-x-2">
                <input type="text" wire:model="block.data.logo.image" 
                       class="flex-1 p-2 border border-gray-300 rounded-md" 
                       placeholder="Image URL" required>
                <button type="button" 
                        wire:click="$emit('openMediaLibrary', 'logo.image')"
                        class="px-3 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                    📁 Select
                </button>
            </div>
            @if($block['data']['logo']['image'])
            <div class="mt-2">
                <img src="{{ $block['data']['logo']['image'] }}" 
                     alt="Logo preview" 
                     class="w-32 h-16 object-contain">
            </div>
            @endif
        </div>
        @endif

        <!-- Logo Styles -->
        <div class="mt-4 p-3 bg-white rounded border">
            <h5 class="font-medium text-gray-700 mb-2">Logo Styles</h5>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Color</label>
                    <input type="color" wire:model="block.data.logo.styles.color" 
                           class="w-full h-8 p-1 border border-gray-300 rounded">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Font Size</label>
                    <input type="text" wire:model="block.data.logo.styles.font_size" 
                           class="w-full p-1 border border-gray-300 rounded text-xs" 
                           placeholder="24px">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Font Weight</label>
                    <select wire:model="block.data.logo.styles.font_weight" 
                            class="w-full p-1 border border-gray-300 rounded text-xs">
                        <option value="normal">Normal</option>
                        <option value="bold">Bold</option>
                        <option value="semibold">Semibold</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Items -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Menu Items</label>
        
        <div class="space-y-3">
            @foreach($block['data']['menu_items'] as $index => $item)
            <div class="menu-item bg-gray-50 p-4 rounded-lg border">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="font-medium">Menu Item #{{ $index + 1 }}</h4>
                    <button type="button" 
                            wire:click="removeRepeaterItem('menu_items', {{ $index }})"
                            class="text-red-600 hover:text-red-800 text-sm">
                        Remove
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Label *</label>
                        <input type="text" wire:model="block.data.menu_items.{{ $index }}.label" 
                               class="w-full p-2 border border-gray-300 rounded-md" 
                               placeholder="Menu label" required>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">URL *</label>
                        <input type="text" wire:model="block.data.menu_items.{{ $index }}.url" 
                               class="w-full p-2 border border-gray-300 rounded-md" 
                               placeholder="/page" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Target</label>
                        <select wire:model="block.data.menu_items.{{ $index }}.target" 
                                class="w-full p-2 border border-gray-300 rounded-md">
                            <option value="_self">Same Tab</option>
                            <option value="_blank">New Tab</option>
                        </select>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" 
                               wire:model="block.data.menu_items.{{ $index }}.is_button"
                               id="is-button-{{ $index }}"
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                        <label for="is-button-{{ $index }}" class="ml-2 text-sm text-gray-600">
                            Style as Button
                        </label>
                    </div>
                </div>

                <!-- Item Styles -->
                <div class="p-3 bg-white rounded border">
                    <h5 class="font-medium text-gray-700 mb-2">Item Styles</h5>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Color</label>
                            <input type="color" wire:model="block.data.menu_items.{{ $index }}.styles.color" 
                                   class="w-full h-8 p-1 border border-gray-300 rounded">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Hover Color</label>
                            <input type="color" wire:model="block.data.menu_items.{{ $index }}.styles.hover_color" 
                                   class="w-full h-8 p-1 border border-gray-300 rounded">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Background</label>
                            <input type="color" wire:model="block.data.menu_items.{{ $index }}.styles.background_color" 
                                   class="w-full h-8 p-1 border border-gray-300 rounded">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Hover Background</label>
                            <input type="color" wire:model="block.data.menu_items.{{ $index }}.styles.hover_background" 
                                   class="w-full h-8 p-1 border border-gray-300 rounded">
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <button type="button" 
                wire:click="addRepeaterItem('menu_items')"
                class="mt-3 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
            + Add Menu Item
        </button>
    </div>

    <!-- Header Styles -->
    <div class="p-4 bg-blue-50 rounded-lg">
        <h4 class="font-medium text-blue-800 mb-3">Header Styles</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-gray-600 mb-1">Background Color</label>
                <input type="color" wire:model="block.data.styles.background_color" 
                       class="w-full h-10 p-1 border border-gray-300 rounded-md">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Text Color</label>
                <input type="color" wire:model="block.data.styles.text_color" 
                       class="w-full h-10 p-1 border border-gray-300 rounded-md">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Padding</label>
                <input type="text" wire:model="block.data.styles.padding" 
                       class="w-full p-2 border border-gray-300 rounded-md" 
                       placeholder="1rem 0">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Shadow</label>
                <select wire:model="block.data.styles.shadow" class="w-full p-2 border border-gray-300 rounded-md">
                    <option value="none">None</option>
                    <option value="sm">Small</option>
                    <option value="md">Medium</option>
                    <option value="lg">Large</option>
                </select>
            </div>
            <div class="flex items-center">
                <input type="checkbox" 
                       wire:model="block.data.styles.sticky"
                       id="sticky-header"
                       class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                <label for="sticky-header" class="ml-2 text-sm text-gray-600">
                    Sticky Header
                </label>
            </div>
        </div>
    </div>
</div>