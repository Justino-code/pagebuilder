<div class="media-library h-full flex flex-col">
    <!-- Header -->
    <div class="bg-white border-b p-4">
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-bold">Media Library</h2>
            <button wire:click="$emit('close-media-library')" class="text-gray-500 hover:text-gray-700">
                ✕
            </button>
        </div>
        
        <div class="flex space-x-2 mt-4">
            <input 
                type="text" 
                wire:model="searchTerm"
                placeholder="Search images..." 
                class="flex-1 p-2 border rounded"
            >
            <button 
                wire:click="$set('showUploadModal', true)"
                class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
            >
                Upload
            </button>
        </div>
    </div>
    
    <!-- Content -->
    <div class="flex-1 overflow-auto p-4">
        @if(count($filteredImages) > 0)
            <div class="grid grid-cols-4 gap-4">
                @foreach($filteredImages as $imagePath)
                    @php
                        $disk = config('pagebuilder.media.disk', 'public');
                        $imageUrl = Storage::disk($disk)->url($imagePath);
                    @endphp
                    
                    <div 
                        class="border rounded overflow-hidden cursor-pointer {{ $selectedImage === $imageUrl ? 'border-blue-500 ring-2 ring-blue-200' : 'border-gray-300' }}"
                        wire:click="selectImage('{{ $imagePath }}')"
                    >
                        <img 
                            src="{{ $imageUrl }}" 
                            alt="Media library image" 
                            class="w-full h-32 object-cover"
                        >
                        <div class="p-2 text-xs truncate">
                            {{ basename($imagePath) }}
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 text-gray-500">
                <div class="text-4xl mb-4">🖼️</div>
                <p>No images found.</p>
                <button 
                    wire:click="$set('showUploadModal', true)"
                    class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                >
                    Upload your first image
                </button>
            </div>
        @endif
    </div>
    
    <!-- Footer -->
    <div class="bg-gray-100 border-t p-4">
        <div class="flex justify-end">
            <button 
                wire:click="confirmSelection"
                class="px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                {{ !$selectedImage ? 'disabled' : '' }}
            >
                Select Image
            </button>
        </div>
    </div>
    
    <!-- Upload Modal -->
    @if($showUploadModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg w-96 p-6">
                <h3 class="text-lg font-bold mb-4">Upload Images</h3>
                
                <input 
                    type="file" 
                    wire:model="uploadedImages" 
                    multiple 
                    class="w-full p-2 border rounded"
                >
                
                @error('uploadedImages.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                
                <div class="flex justify-end space-x-2 mt-6">
                    <button 
                        wire:click="$set('showUploadModal', false)"
                        class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300"
                    >
                        Cancel
                    </button>
                    <button 
                        wire:click="uploadImages"
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                    >
                        Upload
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>