<div class="block-editor text-editor p-4 bg-white rounded-lg shadow-sm border">
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Content</label>
        <textarea 
            wire:model="block.data.content" 
            class="w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 min-h-32"
            placeholder="Enter your text content"
            x-data
            x-init="window.initRichTextEditor($el)"></textarea>
        @error('block.data.content')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    
    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Text Alignment</label>
            <select wire:model="block.data.text_align" class="w-full p-2 border border-gray-300 rounded-md">
                <option value="left">Left</option>
                <option value="center">Center</option>
                <option value="right">Right</option>
                <option value="justify">Justify</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Max Width</label>
            <input 
                type="text" 
                wire:model="block.data.max_width" 
                class="w-full p-2 border border-gray-300 rounded-md"
                placeholder="800px, 90%, none">
        </div>
    </div>
    
    <div class="flex justify-end space-x-3 pt-4 border-t">
        <button 
            wire:click="save" 
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            Save Changes
        </button>
        <button 
            wire:click="cancel" 
            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-400">
            Cancel
        </button>
    </div>
</div>