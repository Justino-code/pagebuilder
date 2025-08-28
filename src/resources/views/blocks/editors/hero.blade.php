<div class="hero-editor space-y-4">
    <!-- Title -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
        <input type="text" wire:model="block.data.title" 
               class="w-full p-2 border border-gray-300 rounded-md" 
               placeholder="Enter hero title" required>
    </div>

    <!-- Subtitle -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Subtitle</label>
        <input type="text" wire:model="block.data.subtitle" 
               class="w-full p-2 border border-gray-300 rounded-md" 
               placeholder="Enter hero subtitle">
    </div>

    <!-- Background Image -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Background Image</label>
        <div class="flex space-x-2">
            <input type="text" wire:model="block.data.background_image" 
                   class="flex-1 p-2 border border-gray-300 rounded-md" 
                   placeholder="Image URL">
            <button type="button" 
                    wire:click="$emit('openMediaLibrary', 'background_image')"
                    class="px-3 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                📁 Select
            </button>
        </div>
        @if($block['data']['background_image'])
            <div class="mt-2">
                <img src="{{ $block['data']['background_image'] }}" 
                     alt="Background preview" 
                     class="w-full h-32 object-cover rounded-md">
            </div>
        @endif
    </div>

    <!-- CTA Section -->
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Button Text</label>
            <input type="text" wire:model="block.data.cta_text" 
                   class="w-full p-2 border border-gray-300 rounded-md" 
                   placeholder="Button text">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Button Link</label>
            <input type="text" wire:model="block.data.cta_link" 
                   class="w-full p-2 border border-gray-300 rounded-md" 
                   placeholder="Button link">
        </div>
    </div>

    <!-- Colors -->
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Text Color</label>
            <input type="color" wire:model="block.data.text_color" 
                   class="w-full h-10 p-1 border border-gray-300 rounded-md">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Overlay Color</label>
            <input type="color" wire:model="block.data.overlay_color" 
                   class="w-full h-10 p-1 border border-gray-300 rounded-md">
        </div>
    </div>

    <!-- Layout -->
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Content Alignment</label>
            <select wire:model="block.data.content_align" class="w-full p-2 border border-gray-300 rounded-md">
                <option value="left">Left</option>
                <option value="center">Center</option>
                <option value="right">Right</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Height</label>
            <input type="text" wire:model="block.data.min_height" 
                   class="w-full p-2 border border-gray-300 rounded-md" 
                   placeholder="500px, 80vh">
        </div>
    </div>
</div>