<div class="page-builder-editor">
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-md p-4">
            <h2 class="text-lg font-bold mb-4">Page Builder</h2>
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Page Title</label>
                <input type="text" wire:model="title" class="w-full p-2 border rounded">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Slug</label>
                <input type="text" wire:model="slug" class="w-full p-2 border rounded">
            </div>
            
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" wire:model="published" class="mr-2">
                    <span class="text-sm">Published</span>
                </label>
            </div>
            
            <div class="mb-6">
                <h3 class="font-medium mb-2">Blocks</h3>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($availableBlocks as $block)
                        <button 
                            wire:click="addBlock('{{ $block['type'] }}')"
                            class="p-2 border rounded text-center hover:bg-gray-50"
                            title="{{ $block['label'] }}"
                        >
                            <div class="text-2xl mb-1">{{ $block['icon'] }}</div>
                            <div class="text-xs">{{ Str::limit($block['label'], 10) }}</div>
                        </button>
                    @endforeach
                </div>
            </div>
            
            <button 
                wire:click="savePage" 
                class="w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600"
            >
                Save Page
            </button>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Toolbar -->
            <div class="bg-white shadow-sm p-4">
                <div class="flex space-x-2">
                    <button 
                        wire:click="openMediaLibrary"
                        class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300"
                    >
                        Media Library
                    </button>
                    <button 
                        wire:click="$set('activeTab', 'content')"
                        class="px-4 py-2 {{ $activeTab === 'content' ? 'bg-blue-500 text-white' : 'bg-gray-200' }} rounded"
                    >
                        Content
                    </button>
                    <button 
                        wire:click="$set('activeTab', 'style')"
                        class="px-4 py-2 {{ $activeTab === 'style' ? 'bg-blue-500 text-white' : 'bg-gray-200' }} rounded"
                    >
                        Style
                    </button>
                    <button 
                        wire:click="$set('activeTab', 'settings')"
                        class="px-4 py-2 {{ $activeTab === 'settings' ? 'bg-blue-500 text-white' : 'bg-gray-200' }} rounded"
                    >
                        Settings
                    </button>
                </div>
            </div>
            
            <!-- Content Area -->
            <div class="flex-1 overflow-auto p-4">
                @if($activeTab === 'content')
                    <div class="space-y-4">
                        @foreach($blocks as $index => $block)
                            <livewire:page-builder-block 
                                :key="'block-'.$index"
                                :index="{{ $index }}"
                                :block="{{ json_encode($block) }}"
                                :isSelected="{{ $selectedBlockIndex === $index ? 'true' : 'false' }}"
                                wire:click="selectBlock({{ $index }})"
                            />
                        @endforeach
                        
                        @if(count($blocks) === 0)
                            <div class="text-center py-12 text-gray-500">
                                <div class="text-4xl mb-4">📄</div>
                                <p>No blocks added yet. Click on a block type to get started.</p>
                            </div>
                        @endif
                    </div>
                @elseif($activeTab === 'style')
                    <div>
                        <h3 class="font-medium mb-2">Custom CSS</h3>
                        <textarea 
                            wire:model="customCss" 
                            class="w-full h-64 p-2 border rounded font-mono"
                            placeholder="Add custom CSS here..."
                        ></textarea>
                    </div>
                @elseif($activeTab === 'settings')
                    <div class="space-y-4">
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="headerEnabled" class="mr-2">
                                <span>Enable Header</span>
                            </label>
                        </div>
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="footerEnabled" class="mr-2">
                                <span>Enable Footer</span>
                            </label>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Media Library Modal -->
    @if($showMediaLibrary)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg w-4/5 h-4/5">
                <livewire:media-library />
            </div>
        </div>
    @endif
</div>