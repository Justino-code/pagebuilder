<div class="cards-editor space-y-6">
    <!-- Title -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Section Title</label>
        <input type="text" wire:model="block.data.title" 
               class="w-full p-2 border border-gray-300 rounded-md" 
               placeholder="Section title">
    </div>

    <!-- Columns -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Columns</label>
        <select wire:model="block.data.columns" class="w-full p-2 border border-gray-300 rounded-md">
            <option value="1">1 Column</option>
            <option value="2">2 Columns</option>
            <option value="3">3 Columns</option>
            <option value="4">4 Columns</option>
        </select>
    </div>

    <!-- Cards Repeater -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Cards</label>
        
        <div class="space-y-4">
            @foreach($block['data']['cards'] as $index => $card)
            <div class="card-item bg-gray-50 p-4 rounded-lg border">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="font-medium">Card #{{ $index + 1 }}</h4>
                    <button type="button" 
                            wire:click="removeRepeaterItem('cards', {{ $index }})"
                            class="text-red-600 hover:text-red-800 text-sm">
                        Remove
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Card Title -->
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Title *</label>
                        <input type="text" wire:model="block.data.cards.{{ $index }}.title" 
                               class="w-full p-2 border border-gray-300 rounded-md" 
                               placeholder="Card title" required>
                    </div>

                    <!-- Card Icon -->
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Icon</label>
                        <input type="text" wire:model="block.data.cards.{{ $index }}.icon" 
                               class="w-full p-2 border border-gray-300 rounded-md" 
                               placeholder="Emoji or icon">
                    </div>
                </div>

                <!-- Card Description -->
                <div class="mt-3">
                    <label class="block text-sm text-gray-600 mb-1">Description</label>
                    <textarea wire:model="block.data.cards.{{ $index }}.description" 
                              rows="2"
                              class="w-full p-2 border border-gray-300 rounded-md" 
                              placeholder="Card description"></textarea>
                </div>

                <!-- Card Image -->
                <div class="mt-3">
                    <label class="block text-sm text-gray-600 mb-1">Image</label>
                    <div class="flex space-x-2">
                        <input type="text" wire:model="block.data.cards.{{ $index }}.image" 
                               class="flex-1 p-2 border border-gray-300 rounded-md" 
                               placeholder="Image URL">
                        <button type="button" 
                                wire:click="$emit('openMediaLibrary', 'cards.{{ $index }}.image')"
                                class="px-3 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                            📁 Select
                        </button>
                    </div>
                </div>

                <!-- Card Link -->
                <div class="mt-3">
                    <label class="block text-sm text-gray-600 mb-1">Link</label>
                    <input type="text" wire:model="block.data.cards.{{ $index }}.link" 
                           class="w-full p-2 border border-gray-300 rounded-md" 
                           placeholder="Card link">
                </div>
            </div>
            @endforeach
        </div>

        <!-- Add Card Button -->
        <button type="button" 
                wire:click="addRepeaterItem('cards')"
                class="mt-3 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
            + Add Card
        </button>
    </div>
</div>