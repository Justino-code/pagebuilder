<div class="page-builder-editor">
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-md p-4">
            <h2 class="text-lg font-bold mb-4">{{ __('pagebuilder::messages.page_builder') }}</h2>
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">{{ __('pagebuilder::messages.title') }}</label>
                <input type="text" wire:model="title" class="w-full p-2 border rounded">
                @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">{{ __('pagebuilder::messages.slug') }}</label>
                <input type="text" wire:model="slug" class="w-full p-2 border rounded">
                @error('slug') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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
                            class="p-2 border rounded text-center hover:bg-gray-50 transition-colors"
                            title="{{ $block['label'] }}"
                        >
                            <div class="text-2xl mb-1">{{ $block['icon'] }}</div>
                            <div class="text-xs">{{ __('pagebuilder::messages.'.$block['label']) }}</div>
                        </button>
                    @endforeach
                </div>
            </div>
            
            <div class="space-y-2">
                <button 
                    wire:click="save" 
                    class="w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition-colors"
                >
                    {{ __('pagebuilder::messages.save') }}
                </button>
                
                @if(!$isNew)
                <button 
                    wire:click="delete" 
                    onclick="return confirm('{{ __('pagebuilder::messages.confirm_delete_page') }}')"
                    class="w-full bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600 transition-colors"
                >
                    {{ __('pagebuilder::messages.delete') }}
                </button>
                @endif
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Toolbar -->
            <div class="bg-white shadow-sm p-4">
                <div class="flex flex-wrap gap-2">
                    <button 
                        wire:click="openMediaLibrary"
                        class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition-colors"
                    >
                        {{ __('pagebuilder::messages.media_library') }}
                    </button>
                    <button 
                        wire:click="$set('activeTab', 'content')"
                        class="px-4 py-2 {{ $activeTab === 'content' ? 'bg-blue-500 text-white' : 'bg-gray-200' }} rounded transition-colors"
                    >
                        {{ __('pagebuilder::messages.content') }}
                    </button>
                    <button 
                        wire:click="$set('activeTab', 'style')"
                        class="px-4 py-2 {{ $activeTab === 'style' ? 'bg-blue-500 text-white' : 'bg-gray-200' }} rounded transition-colors"
                    >
                        {{ __('pagebuilder::messages.style') }}
                    </button>
                    <button 
                        wire:click="$set('activeTab', 'settings')"
                        class="px-4 py-2 {{ $activeTab === 'settings' ? 'bg-blue-500 text-white' : 'bg-gray-200' }} rounded transition-colors"
                    >
                        {{ __('pagebuilder::messages.settings') }}
                    </button>
                    <button 
                        wire:click="openStyleEditor"
                        class="px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600 transition-colors"
                    >
                        🎨 {{ __('pagebuilder::messages.visual_style') }}
                    </button>
                </div>
            </div>
            
            <!-- Content Area -->
            <div class="flex-1 overflow-auto p-4">
                @if($activeTab === 'content')
                    <div class="space-y-4 min-h-full">
                        @forelse($content as $index => $block)
                            <div 
                                wire:key="block-{{ $index }}"
                                wire:click="selectBlock({{ $index }})"
                                class="cursor-pointer"
                            >
                                <livewire:page-builder-block 
                                    :index="$index"
                                    :block="$block"
                                    :isSelected="$selectedBlockIndex === $index"
                                    :key="'block-'.$index"
                                />
                            </div>
                        @empty
                            <div class="text-center py-12 text-gray-500">
                                <div class="text-4xl mb-4">📄</div>
                                <p class="text-lg">{{ __('pagebuilder::messages.no_blocks_added') }}</p>
                                <p class="text-sm mt-2">{{ __('pagebuilder::messages.click_block_to_start') }}</p>
                            </div>
                        @endforelse
                    </div>
                
                @elseif($activeTab === 'style')
                    <div class="space-y-4">
                        <div>
                            <h3 class="font-medium mb-2">{{ __('pagebuilder::messages.custom_css') }}</h3>
                            <textarea 
                                wire:model="customCss" 
                                class="w-full h-64 p-2 border rounded font-mono text-sm"
                                placeholder="{{ __('pagebuilder::messages.add_custom_css') }}"
                                spellcheck="false"
                            ></textarea>
                        </div>
                        
                        <div>
                            <h3 class="font-medium mb-2">{{ __('pagebuilder::messages.custom_js') }}</h3>
                            <textarea 
                                wire:model="customJs" 
                                class="w-full h-32 p-2 border rounded font-mono text-sm"
                                placeholder="{{ __('pagebuilder::messages.add_custom_js') }}"
                                spellcheck="false"
                            ></textarea>
                        </div>
                    </div>
                
                @elseif($activeTab === 'settings')
                    <div class="space-y-4 max-w-md">
                        <div class="p-4 bg-white rounded-lg shadow">
                            <h3 class="font-medium mb-3">{{ __('pagebuilder::messages.page_settings') }}</h3>
                            
                            <div class="space-y-3">
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
                                
                                <div>
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="published" class="mr-2">
                                        <span>{{ __('pagebuilder::messages.publish_page') }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        @if(!$isNew)
                        <div class="p-4 bg-yellow-50 rounded-lg shadow">
                            <h3 class="font-medium mb-3 text-yellow-800">{{ __('pagebuilder::messages.danger_zone') }}</h3>
                            <button 
                                wire:click="delete" 
                                onclick="return confirm('{{ __('pagebuilder::messages.confirm_delete_page') }}')"
                                class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors"
                            >
                                {{ __('pagebuilder::messages.delete_page') }}
                            </button>
                        </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Media Library Modal -->
    @if($showMediaLibrary)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg w-full h-full max-w-6xl max-h-[90vh]">
                <livewire:media-library />
            </div>
        </div>
    @endif

    <!-- Style Editor Modal -->
    @if($showStyleEditor)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg w-full h-full max-w-4xl max-h-[90vh] overflow-auto">
                <livewire:style-editor :initialStyles="$pageStyles" />
            </div>
        </div>
    @endif
    
    <!-- Loading Overlay -->
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
            <p class="mt-3 text-center">{{ __('pagebuilder::messages.saving') }}...</p>
        </div>
    </div>
</div>

@push('styles')
<style>
    .page-builder-editor {
        font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
    }
    
    .transition-colors {
        transition: background-color 0.2s ease, color 0.2s ease;
    }
    
    /* Custom scrollbar for content area */
    .overflow-auto::-webkit-scrollbar {
        width: 8px;
    }
    
    .overflow-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    .overflow-auto::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }
    
    .overflow-auto::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
</style>
@endpush

@push('scripts')
<script>
    // Auto-slug generation from title
    document.addEventListener('livewire:load', function() {
        const titleInput = document.querySelector('input[wire\\:model="title"]');
        const slugInput = document.querySelector('input[wire\\:model="slug"]');
        
        if (titleInput && slugInput) {
            titleInput.addEventListener('input', function(e) {
                // Only generate slug if slug field is empty or matches the old title
                if (!slugInput.value || slugInput.value === window.lastTitleSlug) {
                    const slug = e.target.value
                        .toLowerCase()
                        .trim()
                        .replace(/[^a-z0-9\s-]/g, '')
                        .replace(/[\s-]+/g, '-')
                        .replace(/^-+|-+$/g, '');
                    
                    slugInput.value = slug;
                    window.lastTitleSlug = slug;
                    
                    // Update Livewire model
                    slugInput.dispatchEvent(new Event('input'));
                }
            });
        }
    });
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl+S or Cmd+S to save
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            Livewire.emit('savePage');
        }
        
        // Delete selected block
        if (e.key === 'Delete' && Livewire.get('selectedBlockIndex') !== null) {
            e.preventDefault();
            Livewire.emit('blockRemoved', Livewire.get('selectedBlockIndex'));
        }
    });
</script>
@endpush