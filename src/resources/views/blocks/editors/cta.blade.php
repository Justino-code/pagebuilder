<div class="cta-editor space-y-4">
    <!-- Title -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
        <input type="text" wire:model="block.data.title" 
               class="w-full p-2 border border-gray-300 rounded-md" 
               placeholder="Enter CTA title" required>
    </div>

    <!-- Description -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
        <textarea wire:model="block.data.description" 
                  rows="3"
                  class="w-full p-2 border border-gray-300 rounded-md" 
                  placeholder="Enter description"></textarea>
    </div>

    <!-- Button -->
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Button Text *</label>
            <input type="text" wire:model="block.data.button_text" 
                   class="w-full p-2 border border-gray-300 rounded-md" 
                   placeholder="Button text" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Button Link *</label>
            <input type="text" wire:model="block.data.button_link" 
                   class="w-full p-2 border border-gray-300 rounded-md" 
                   placeholder="Button link" required>
        </div>
    </div>

    <!-- Layout -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Layout</label>
        <select wire:model="block.data.layout" class="w-full p-2 border border-gray-300 rounded-md">
            <option value="centered">Centered</option>
            <option value="left">Left Aligned</option>
            <option value="split">Split Layout</option>
        </select>
    </div>

    <!-- Colors -->
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Background Color</label>
            <input type="color" wire:model="block.data.background_color" 
                   class="w-full h-10 p-1 border border-gray-300 rounded-md">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Text Color</label>
            <input type="color" wire:model="block.data.text_color" 
                   class="w-full h-10 p-1 border border-gray-300 rounded-md">
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Button Color</label>
            <input type="color" wire:model="block.data.button_color" 
                   class="w-full h-10 p-1 border border-gray-300 rounded-md">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Button Text Color</label>
            <input type="color" wire:model="block.data.button_text_color" 
                   class="w-full h-10 p-1 border border-gray-300 rounded-md">
        </div>
    </div>
</div>