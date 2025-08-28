<div class="gallery-editor space-y-6">
    <!-- Basic Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Gallery Title</label>
            <input type="text" wire:model="block.data.title" 
                   class="w-full p-2 border border-gray-300 rounded-md" 
                   placeholder="Gallery title">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Columns</label>
            <select wire:model="block.data.columns" class="w-full p-2 border border-gray-300 rounded-md">
                <option value="2">2 Columns</option>
                <option value="3">3 Columns</option>
                <option value="4">4 Columns</option>
                <option value="5">5 Columns</option>
                <option value="6">6 Columns</option>
            </select>
        </div>
    </div>

    <!-- Description -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
        <textarea wire:model="block.data.description" 
                  rows="2"
                  class="w-full p-2 border border-gray-300 rounded-md" 
                  placeholder="Gallery description"></textarea>
    </div>

    <!-- Settings -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Aspect Ratio</label>
            <select wire:model="block.data.image_aspect_ratio" class="w-full p-2 border border-gray-300 rounded-md">
                <option value="1/1">Square (1:1)</option>
                <option value="4/3">Standard (4:3)</option>
                <option value="16/9">Widescreen (16:9)</option>
                <option value="3/2">Classic (3:2)</option>
                <option value="free">Free (Original)</option>
            </select>
        </div>
        
        <div class="flex items-center">
            <input type="checkbox" 
                   wire:model="block.data.show_captions"
                   id="show-captions"
                   class="w-4 h-4 text-blue-600 border-gray-300 rounded">
            <label for="show-captions" class="ml-2 text-sm text-gray-700">
                Show Captions
            </label>
        </div>
        
        <div class="flex items-center">
            <input type="checkbox" 
                   wire:model="block.data.lightbox_enabled"
                   id="lightbox-enabled"
                   class="w-4 h-4 text-blue-600 border-gray-300 rounded">
            <label for="lightbox-enabled" class="ml-2 text-sm text-gray-700">
                Enable Lightbox
            </label>
        </div>
    </div>

    <!-- Images Repeater -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Images</label>
        
        <div class="space-y-4">
            @foreach($block['data']['images'] as $index => $image)
            <div class="image-item bg-gray-50 p-4 rounded-lg border">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="font-medium">Image #{{ $index + 1 }}</h4>
                    <button type="button" 
                            wire:click="removeRepeaterItem('images', {{ $index }})"
                            class="text-red-600 hover:text-red-800 text-sm">
                        Remove
                    </button>
                </div>

                <!-- Image URL -->
                <div class="mb-3">
                    <label class="block text-sm text-gray-600 mb-1">Image URL *</label>
                    <div class="flex space-x-2">
                        <input type="text" wire:model="block.data.images.{{ $index }}.image" 
                               class="flex-1 p-2 border border-gray-300 rounded-md" 
                               placeholder="Image URL" required>
                        <button type="button" 
                                wire:click="$emit('openMediaLibrary', 'images.{{ $index }}.image')"
                                class="px-3 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                            📁 Select
                        </button>
                    </div>
                    @if($image['image'])
                    <div class="mt-2">
                        <img src="{{ $image['image'] }}" 
                             alt="Preview" 
                             class="w-full h-24 object-cover rounded-md">
                    </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Caption -->
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Caption</label>
                        <input type="text" wire:model="block.data.images.{{ $index }}.caption" 
                               class="w-full p-2 border border-gray-300 rounded-md" 
                               placeholder="Image caption">
                    </div>

                    <!-- Alt Text -->
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Alt Text</label>
                        <input type="text" wire:model="block.data.images.{{ $index }}.alt_text" 
                               class="w-full p-2 border border-gray-300 rounded-md" 
                               placeholder="Alternative text">
                    </div>
                </div>

                <!-- Link -->
                <div class="mt-3">
                    <label class="block text-sm text-gray-600 mb-1">Link</label>
                    <input type="text" wire:model="block.data.images.{{ $index }}.link" 
                           class="w-full p-2 border border-gray-300 rounded-md" 
                           placeholder="Image link (optional)">
                </div>
            </div>
            @endforeach
        </div>

        <!-- Add Image Button -->
        <button type="button" 
                wire:click="addRepeaterItem('images')"
                class="mt-3 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
            + Add Image
        </button>
    </div>

    <!-- Styles -->
    <div class="p-4 bg-blue-50 rounded-lg">
        <h4 class="font-medium text-blue-800 mb-3">Gallery Styles</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm text-gray-600 mb-1">Gap Between Images</label>
                <input type="text" wire:model="block.data.styles.gap" 
                       class="w-full p-2 border border-gray-300 rounded-md" 
                       placeholder="0.5rem">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Border Radius</label>
                <input type="text" wire:model="block.data.styles.border_radius" 
                       class="w-full p-2 border border-gray-300 rounded-md" 
                       placeholder="0.5rem">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Hover Effect</label>
                <select wire:model="block.data.styles.hover_effect" class="w-full p-2 border border-gray-300 rounded-md">
                    <option value="none">None</option>
                    <option value="zoom">Zoom</option>
                    <option value="grayscale">Grayscale</option>
                    <option value="shadow">Shadow</option>
                </select>
            </div>
        </div>
    </div>
</div>