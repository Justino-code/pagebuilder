<div class="form-editor space-y-6">
    <!-- Basic Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Form Title</label>
            <input type="text" wire:model="block.data.title" 
                   class="w-full p-2 border border-gray-300 rounded-md" 
                   placeholder="Form title">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email Recipient *</label>
            <input type="email" wire:model="block.data.email_recipient" 
                   class="w-full p-2 border border-gray-300 rounded-md" 
                   placeholder="admin@example.com" required>
        </div>
    </div>

    <!-- Description -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
        <textarea wire:model="block.data.description" 
                  rows="2"
                  class="w-full p-2 border border-gray-300 rounded-md" 
                  placeholder="Form description"></textarea>
    </div>

    <!-- Submit Text -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Submit Button Text *</label>
            <input type="text" wire:model="block.data.submit_text" 
                   class="w-full p-2 border border-gray-300 rounded-md" 
                   placeholder="Send Message" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Success Message</label>
            <input type="text" wire:model="block.data.success_message" 
                   class="w-full p-2 border border-gray-300 rounded-md" 
                   placeholder="Thank you message">
        </div>
    </div>

    <!-- Form Fields Repeater -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Form Fields</label>
        
        <div class="space-y-4">
            @foreach($block['data']['fields'] as $index => $field)
            <div class="field-item bg-gray-50 p-4 rounded-lg border">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="font-medium">Field #{{ $index + 1 }}</h4>
                    <button type="button" 
                            wire:click="removeRepeaterItem('fields', {{ $index }})"
                            class="text-red-600 hover:text-red-800 text-sm">
                        Remove
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                    <!-- Field Type -->
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Field Type *</label>
                        <select wire:model="block.data.fields.{{ $index }}.type" 
                                class="w-full p-2 border border-gray-300 rounded-md">
                            <option value="text">Text</option>
                            <option value="email">Email</option>
                            <option value="tel">Phone</option>
                            <option value="textarea">Textarea</option>
                            <option value="select">Select</option>
                            <option value="checkbox">Checkbox</option>
                        </select>
                    </div>

                    <!-- Field Name -->
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Field Name *</label>
                        <input type="text" wire:model="block.data.fields.{{ $index }}.name" 
                               class="w-full p-2 border border-gray-300 rounded-md" 
                               placeholder="e.g., name, email" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                    <!-- Field Label -->
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Field Label *</label>
                        <input type="text" wire:model="block.data.fields.{{ $index }}.label" 
                               class="w-full p-2 border border-gray-300 rounded-md" 
                               placeholder="Field label" required>
                    </div>

                    <!-- Placeholder -->
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Placeholder</label>
                        <input type="text" wire:model="block.data.fields.{{ $index }}.placeholder" 
                               class="w-full p-2 border border-gray-300 rounded-md" 
                               placeholder="Placeholder text">
                    </div>
                </div>

                <!-- Required -->
                <div class="flex items-center mb-3">
                    <input type="checkbox" 
                           wire:model="block.data.fields.{{ $index }}.required"
                           id="field-{{ $index }}-required"
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                    <label for="field-{{ $index }}-required" class="ml-2 text-sm text-gray-600">
                        Required field
                    </label>
                </div>

                <!-- Options (for select) -->
                @if(($block['data']['fields'][$index]['type'] ?? 'text') === 'select')
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Options</label>
                    <textarea wire:model="block.data.fields.{{ $index }}.options" 
                              rows="3"
                              class="w-full p-2 border border-gray-300 rounded-md" 
                              placeholder="One option per line:
value:Label
option2:Second Option"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Format: value:Label (one per line)</p>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <!-- Add Field Button -->
        <button type="button" 
                wire:click="addRepeaterItem('fields')"
                class="mt-3 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
            + Add Field
        </button>
    </div>

    <!-- Styles -->
    <div class="p-4 bg-blue-50 rounded-lg">
        <h4 class="font-medium text-blue-800 mb-3">Form Styles</h4>
        <div class="grid grid-cols-2 gap-4">
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
                <label class="block text-sm text-gray-600 mb-1">Button Color</label>
                <input type="color" wire:model="block.data.styles.button_color" 
                       class="w-full h-10 p-1 border border-gray-300 rounded-md">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Button Text Color</label>
                <input type="color" wire:model="block.data.styles.button_text_color" 
                       class="w-full h-10 p-1 border border-gray-300 rounded-md">
            </div>
        </div>
    </div>
</div>