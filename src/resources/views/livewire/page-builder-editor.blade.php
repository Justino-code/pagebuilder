<div class="page-builder-editor">
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-md p-4">
            <h2 class="text-lg font-bold mb-4">{{ __('pagebuilder::messages.page_builder') }}</h2>
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">{{ __('pagebuilder::messages.title') }}</label>
                <input type="text" wire:model="title" class="w-full p-2 border rounded">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">{{ __('pagebuilder::messages.slug') }}</label>
                <input type="text" wire:model="slug" class="w-full p-2 border rounded">
            </div>
            
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" wire:model="published" class="mr-2">
                    <span class="text-sm">{{ __('pagebuilder::messages.published') }}</span>
                </label>
            </div>
            
            <div class="mb-6">
                <h3 class="font-medium mb-2">{{ __('pagebuilder::messages.blocks') }}</h3>
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
                wire:click="save" 
                class="w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600"
            >
                {{ __('pagebuilder::messages.save') }}
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
                        {{ __('pagebuilder::messages.media_library') }}
                    </button>
                    <button 
                        wire:click="$set('activeTab', 'content')"
                        class="px-4 py-2 {{ $activeTab === 'content' ? 'bg-blue-500 text-white' : 'bg-gray-200' }} rounded"
                    >
                        {{ __('pagebuilder::messages.content') }}
                    </button>
                    <button 
                        wire:click="$set('activeTab', 'style')"
                        class="px-4 py-2 {{ $activeTab === 'style' ? 'bg-blue-500 text-white' : 'bg-gray-200' }} rounded"
                    >
                        {{ __('pagebuilder::messages.style') }}
                    </button>
                    <button 
                        wire:click="$set('activeTab', 'settings')"
                        class="px-4 py-2 {{ $activeTab === 'settings' ? 'bg-blue-500 text-white' : 'bg-gray-200' }} rounded"
                    >
                        {{ __('pagebuilder::messages.settings') }}
                    </button>
                </div>
            </div>
            
            <!-- Content Area -->
            <div class="flex-1 overflow-auto p-4">
                @if($activeTab === 'content')
                    <div class="space-y-4">
                        @foreach($content as $index => $block)
                            <div wire:key="block-{{ $index }}">
                                @livewire('page-builder-block', [
                                    'index' => $index,
                                    'block' => $block,
                                    'isSelected' => $selectedBlockIndex === $index
                                ], key('block-'.$index))
                            </div>
                        @endforeach
                        
                        @if(count($content) === 0)
                            <div class="text-center py-12 text-gray-500">
                                <div class="text-4xl mb-4">📄</div>
                                <p>{{ __('pagebuilder::messages.no_blocks_added') }}</p>
                            </div>
                        @endif
                    </div>
                @elseif($activeTab === 'style')
                    <div>
                        <h3 class="font-medium mb-2">{{ __('pagebuilder::messages.custom_css') }}</h3>
                        <textarea 
                            wire:model="customCss" 
                            class="w-full h-64 p-2 border rounded font-mono"
                            placeholder="{{ __('pagebuilder::messages.add_custom_css') }}"
                        ></textarea>
                    </div>
                @elseif($activeTab === 'settings')
                    <div class="space-y-4">
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="headerEnabled" class="mr-2">
                                <span>{{ __('pagebuilder::messages.enable_header') }}</span>
                            </label>
                        </div>
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="footerEnabled" class="mr-2">
                                <span>{{ __('pagebuilder::messages.enable_footer') }}</span>
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
                @livewire('media-library')
            </div>
        </div>
    @endif
</div>