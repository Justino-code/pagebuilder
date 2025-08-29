<div class="page-builder-editor">
    <div class="flex h-screen bg-gray-100 dark:bg-gray-900">
        <!-- Sidebar -->
        <div class="w-80 bg-white dark:bg-gray-800 shadow-md p-4 overflow-y-auto">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white">
                    {{ __('pagebuilder::messages.page_builder') }}
                </h2>
                <div class="flex items-center space-x-2">
                    <!-- Theme Toggle -->
                    <button wire:click="$set('theme', '{{ $theme === 'dark' ? 'light' : 'dark' }}')" 
                            class="p-2 rounded-lg bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
                            title="{{ __('pagebuilder::messages.toggle_theme') }}">
                        @if($theme === 'dark')
                            🌞
                        @else
                            🌙
                        @endif
                    </button>
                    
                    <!-- Save Status Indicator -->
                    @if($isSaving)
                        <div class="w-3 h-3 rounded-full bg-blue-500 animate-pulse" title="{{ __('pagebuilder::messages.saving') }}"></div>
                    @elseif($saveStatus === 'success')
                        <div class="w-3 h-3 rounded-full bg-green-500" title="{{ __('pagebuilder::messages.saved') }}"></div>
                    @endif
                </div>
            </div>

            <!-- Page Info -->
            <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ __('pagebuilder::messages.page_info') }}</span>
                    @if(!$isNew)
                    <span class="text-xs px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full">
                        v{{ $version }}
                    </span>
                    @endif
                </div>
                
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">{{ __('pagebuilder::messages.title') }} *</label>
                        <input type="text" wire:model="title" 
                               class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                               placeholder="{{ __('pagebuilder::messages.page_title_placeholder') }}">
                        @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">{{ __('pagebuilder::messages.slug') }} *</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-600 text-gray-500 dark:text-gray-400 text-sm">
                                {{ config('app.url') }}/
                            </span>
                            <input type="text" wire:model="slug" 
                                   class="flex-1 p-2 border border-gray-300 dark:border-gray-600 rounded-r bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                   placeholder="{{ __('pagebuilder::messages.page_slug_placeholder') }}">
                        </div>
                        @error('slug') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Status & Actions -->
            <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="space-y-3">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="published" 
                               class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:ring-blue-500 dark:bg-gray-700">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('pagebuilder::messages.published') }}</span>
                    </label>
                    
                    <div class="flex space-x-2">
                        <button wire:click="saveDraft" 
                                wire:loading.attr="disabled"
                                class="flex-1 px-3 py-2 bg-gray-600 text-white text-sm rounded hover:bg-gray-700 transition-colors disabled:opacity-50">
                            {{ __('pagebuilder::messages.save_draft') }}
                        </button>
                        
                        <button wire:click="publish" 
                                wire:loading.attr="disabled"
                                class="flex-1 px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors disabled:opacity-50">
                            {{ __('pagebuilder::messages.publish') }}
                        </button>
                    </div>
                    
                    @if(!$isNew && $published)
                    <button wire:click="unpublish" 
                            wire:loading.attr="disabled"
                            class="w-full px-3 py-2 bg-yellow-500 text-white text-sm rounded hover:bg-yellow-600 transition-colors disabled:opacity-50">
                        {{ __('pagebuilder::messages.unpublish') }}
                    </button>
                    @endif
                </div>
            </div>

            <!-- Blocks Library -->
            <div class="mb-6">
                <h3 class="font-medium mb-3 text-gray-700 dark:text-gray-300">{{ __('pagebuilder::messages.blocks_library') }}</h3>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($availableBlocks as $block)
                        <button 
                            wire:click="addBlock('{{ $block['type'] }}')"
                            class="p-3 border border-gray-200 dark:border-gray-600 rounded-lg text-center hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors group"
                            title="{{ $block['label'] }}"
                        >
                            <div class="text-2xl mb-2 group-hover:scale-110 transition-transform">{{ $block['icon'] }}</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400 font-medium">{{ $block['label'] }}</div>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Page Settings -->
            <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <h3 class="font-medium mb-3 text-gray-700 dark:text-gray-300">{{ __('pagebuilder::messages.page_settings') }}</h3>
                
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">{{ __('pagebuilder::messages.theme') }}</label>
                        <select wire:model="theme" 
                                class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            @foreach($themeOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="headerEnabled" 
                                   class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:ring-blue-500 dark:bg-gray-700">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('pagebuilder::messages.enable_header') }}</span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="footerEnabled" 
                                   class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:ring-blue-500 dark:bg-gray-700">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('pagebuilder::messages.enable_footer') }}</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Advanced Actions -->
            <div class="space-y-2">
                @if(!$isNew)
                <div class="grid grid-cols-2 gap-2">
                    <button wire:click="duplicate" 
                            wire:loading.attr="disabled"
                            class="px-3 py-2 bg-green-500 text-white text-sm rounded hover:bg-green-600 transition-colors disabled:opacity-50">
                        {{ __('pagebuilder::messages.duplicate') }}
                    </button>
                    
                    <button wire:click="showVersionHistory" 
                            class="px-3 py-2 bg-purple-500 text-white text-sm rounded hover:bg-purple-600 transition-colors">
                        {{ __('pagebuilder::messages.versions') }}
                    </button>
                </div>
                
                <button wire:click="clearCache" 
                        class="w-full px-3 py-2 bg-gray-500 text-white text-sm rounded hover:bg-gray-600 transition-colors">
                    {{ __('pagebuilder::messages.clear_cache') }}
                </button>
                
                <div class="border-t border-gray-200 dark:border-gray-600 pt-2">
                    <button 
                        wire:click="delete" 
                        onclick="return confirm('{{ __('pagebuilder::messages.confirm_delete_page') }}')"
                        class="w-full px-3 py-2 bg-red-500 text-white text-sm rounded hover:bg-red-600 transition-colors"
                    >
                        {{ __('pagebuilder::messages.delete_page') }}
                    </button>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Toolbar -->
            <div class="bg-white dark:bg-gray-800 shadow-sm p-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Tab Navigation -->
                    <button 
                        wire:click="$set('activeTab', 'content')"
                        class="px-4 py-2 rounded-lg transition-colors {{ $activeTab === 'content' ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}"
                    >
                        📝 {{ __('pagebuilder::messages.content') }}
                    </button>
                    
                    <button 
                        wire:click="$set('activeTab', 'style')"
                        class="px-4 py-2 rounded-lg transition-colors {{ $activeTab === 'style' ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}"
                    >
                        🎨 {{ __('pagebuilder::messages.style') }}
                    </button>
                    
                    <button 
                        wire:click="$set('activeTab', 'advanced')"
                        class="px-4 py-2 rounded-lg transition-colors {{ $activeTab === 'advanced' ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}"
                    >
                        ⚙️ {{ __('pagebuilder::messages.advanced') }}
                    </button>

                    <!-- Action Buttons -->
                    <div class="flex-1"></div>

                    <button 
                        wire:click="openMediaLibrary"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
                    >
                        📁 {{ __('pagebuilder::messages.media_library') }}
                    </button>
                    
                    <button 
                        wire:click="openStyleEditor"
                        class="px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:from-purple-600 hover:to-pink-600 transition-colors"
                    >
                        🎨 {{ __('pagebuilder::messages.visual_style') }}
                    </button>

                    <button 
                        wire:click="preview"
                        onclick="window.open('{{ route('pagebuilder.pages.preview', $slug ?? '') }}', '_blank')"
                        class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors"
                        target="_blank"
                    >
                        👁️ {{ __('pagebuilder::messages.preview') }}
                    </button>
                </div>

                <!-- Save Status Message -->
                @if($saveMessage)
                <div class="mt-3 p-2 rounded-lg text-sm {{ $saveStatus === 'success' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                    {{ $saveMessage }}
                </div>
                @endif
            </div>
            
            <!-- Content Area -->
            <div class="flex-1 overflow-auto p-4 bg-gray-50 dark:bg-gray-900">
                @if($activeTab === 'content')
                    <div class="space-y-4 min-h-full">
                        @forelse($content as $index => $block)
                            <div 
                                wire:key="block-{{ $index }}"
                                wire:click="selectBlock({{ $index }})"
                                class="cursor-pointer transition-transform hover:scale-[1.02]"
                            >
                                @php
                                    $blockData = $block['data'] ?? [];
                                    $blockStyles = $block['styles'] ?? [];
                                    
                                    // Validação básica dos dados do bloco
                                    if (!is_array($blockData)) {
                                        $blockData = [];
                                        Log::warning("Block data is not array", ['index' => $index, 'type' => $block['type'] ?? 'unknown']);
                                    }
                                    
                                    if (!is_array($blockStyles)) {
                                        $blockStyles = [];
                                        Log::warning("Block styles is not array", ['index' => $index, 'type' => $block['type'] ?? 'unknown']);
                                    }
                                @endphp
                                
                                <livewire:block-editor 
                                    :blockId="$index"
                                    :blockType="$block['type']"
                                    :initialData="$blockData"
                                    :initialStyles="$blockStyles"
                                    :key="'block-'.$index.'-'.md5(serialize($blockData))"
                                />
                            </div>
                        @empty
                            <div class="text-center py-16 text-gray-400 dark:text-gray-500">
                                <div class="text-6xl mb-4">📄</div>
                                <p class="text-lg font-medium mb-2">{{ __('pagebuilder::messages.no_blocks_added') }}</p>
                                <p class="text-sm">{{ __('pagebuilder::messages.click_block_to_start') }}</p>
                                <div class="mt-6">
                                    <div class="inline-flex flex-col space-y-2">
                                        @foreach(array_slice($availableBlocks, 0, 3) as $block)
                                            <button 
                                                wire:click="addBlock('{{ $block['type'] }}')"
                                                class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                            >
                                                {{ $block['label'] }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>
                
                @elseif($activeTab === 'style')
                    <div class="max-w-4xl mx-auto space-y-6">
                        <!-- Custom CSS -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                            <h3 class="font-medium mb-3 text-gray-800 dark:text-white">
                                {{ __('pagebuilder::messages.custom_css') }}
                            </h3>
                            <div class="relative">
                                <textarea 
                                    wire:model="customCss" 
                                    class="w-full h-96 p-4 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white font-mono text-sm resize-none"
                                    placeholder="/* {{ __('pagebuilder::messages.add_custom_css') }} */"
                                    spellcheck="false"
                                ></textarea>
                                <div class="absolute top-2 right-2">
                                    <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs rounded">
                                        CSS
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Custom JS -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                            <h3 class="font-medium mb-3 text-gray-800 dark:text-white">
                                {{ __('pagebuilder::messages.custom_js') }}
                            </h3>
                            <div class="relative">
                                <textarea 
                                    wire:model="customJs" 
                                    class="w-full h-48 p-4 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white font-mono text-sm resize-none"
                                    placeholder="// {{ __('pagebuilder::messages.add_custom_js') }}"
                                    spellcheck="false"
                                ></textarea>
                                <div class="absolute top-2 right-2">
                                    <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs rounded">
                                        JS
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                
                @elseif($activeTab === 'advanced')
                    <div class="max-w-4xl mx-auto space-y-6">
                        <!-- Import/Export -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                            <h3 class="font-medium mb-4 text-gray-800 dark:text-white">
                                {{ __('pagebuilder::messages.import_export') }}
                            </h3>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <!-- Export -->
                                <div class="text-center">
                                    <button wire:click="export" 
                                            class="w-full px-4 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                                        📤 {{ __('pagebuilder::messages.export_page') }}
                                    </button>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                        {{ __('pagebuilder::messages.export_description') }}
                                    </p>
                                </div>
                                
                                <!-- Import -->
                                <div class="text-center">
                                    <label class="block w-full px-4 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors cursor-pointer">
                                        📥 {{ __('pagebuilder::messages.import_page') }}
                                        <input type="file" wire:model="importFile" class="hidden" accept=".json">
                                    </label>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                        {{ __('pagebuilder::messages.import_description') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Page Meta -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                            <h3 class="font-medium mb-4 text-gray-800 dark:text-white">
                                {{ __('pagebuilder::messages.page_meta') }}
                            </h3>
                            
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">{{ __('pagebuilder::messages.created_at') }}:</span>
                                    <p class="font-medium">{{ $page->created_at ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">{{ __('pagebuilder::messages.updated_at') }}:</span>
                                    <p class="font-medium">{{ $page->updated_at ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">{{ __('pagebuilder::messages.blocks_count') }}:</span>
                                    <p class="font-medium">{{ count($content) }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">{{ __('pagebuilder::messages.versions_count') }}:</span>
                                    <p class="font-medium">{{ count($versions) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Modals -->
    <!-- Media Library Modal -->
    @if($showMediaLibrary)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg w-full h-full max-w-6xl max-h-[90vh] overflow-hidden">
                <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('pagebuilder::messages.media_library') }}
                    </h3>
                    <button wire:click="$set('showMediaLibrary', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        ✕
                    </button>
                </div>
                <livewire:media-library />
            </div>
        </div>
    @endif

    <!-- Style Editor Modal -->
    @if($showStyleEditor)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg w-full h-full max-w-6xl max-h-[90vh] overflow-hidden">
                <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        🎨 {{ __('pagebuilder::messages.visual_style_editor') }}
                    </h3>
                    <button wire:click="$set('showStyleEditor', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        ✕
                    </button>
                </div>
                <livewire:style-editor :initialStyles="$pageStyles" :theme="$theme" />
            </div>
        </div>
    @endif

    <!-- Version History Modal -->
    @if($showVersionHistory)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg w-full max-w-4xl max-h-[80vh] overflow-hidden">
                <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        📚 {{ __('pagebuilder::messages.version_history') }}
                    </h3>
                    <button wire:click="$set('showVersionHistory', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        ✕
                    </button>
                </div>
                <div class="p-4 overflow-y-auto max-h-[60vh]">
                    <div class="space-y-3">
                        @forelse($versions as $version)
                            <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                        v{{ $version['versionNumber'] ?? '1.0.0' }}
                                    </span>
                                    <span class="text-xs px-2 py-1 bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-full">
                                        {{ $version['type'] ?? 'revision' }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                    {{ $version['note'] ?? __('pagebuilder::messages.no_description') }}
                                </p>
                                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                    <span>{{ $version['created_at'] ?? now()->format('d/m/Y H:i') }}</span>
                                    <span>{{ $version['created_by'] ?? auth()->user()->name }}</span>
                                </div>
                                <div class="mt-3">
                                    <button wire:click="restoreVersion('{{ $version['id'] }}')"
                                            class="px-3 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600 transition-colors">
                                        {{ __('pagebuilder::messages.restore_version') }}
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-400 dark:text-gray-500">
                                <div class="text-4xl mb-4">📄</div>
                                <p>{{ __('pagebuilder::messages.no_versions_found') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Loading Overlay -->
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-xl">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
            <p class="mt-3 text-center text-gray-700 dark:text-gray-300">{{ __('pagebuilder::messages.saving') }}...</p>
        </div>
    </div>
</div>

@push('styles')
<style>
    .page-builder-editor {
        font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
    }
    
    .transition-colors {
        transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
    }
    
    .transition-transform {
        transition: transform 0.2s ease;
    }
    
    /* Custom scrollbar */
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
    
    /* Dark mode scrollbar */
    .dark .overflow-auto::-webkit-scrollbar-track {
        background: #374151;
    }
    
    .dark .overflow-auto::-webkit-scrollbar-thumb {
        background: #6b7280;
    }
    
    .dark .overflow-auto::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('livewire:load', function() {
        const titleInput = document.querySelector('input[wire\\:model="title"]');
        const slugInput = document.querySelector('input[wire\\:model="slug"]');
        
        if (titleInput && slugInput) {
            let isManualSlugEdit = false;
            
            slugInput.addEventListener('input', function() {
                isManualSlugEdit = true;
            });
            
            titleInput.addEventListener('input', function(e) {
                if (!isManualSlugEdit || !slugInput.value) {
                    const slug = e.target.value
                        .toLowerCase()
                        .trim()
                        .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                        .replace(/[^a-z0-9\s-]/g, '')
                        .replace(/[\s-]+/g, '-')
                        .replace(/^-+|-+$/g, '');
                    
                    slugInput.value = slug;
                    slugInput.dispatchEvent(new Event('input'));
                }
            });
        }
    });
    
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            Livewire.dispatch('save-draft');
        }
        
        if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
            e.preventDefault();
            Livewire.dispatch('publish');
        }
        
        if (e.key === 'Delete' && Livewire.get('selectedBlockIndex') !== null) {
            e.preventDefault();
            Livewire.dispatch('block-removed', { index: Livewire.get('selectedBlockIndex') });
        }
        
        if (e.key === 'Escape' && Livewire.get('selectedBlockIndex') !== null) {
            e.preventDefault();
            Livewire.set('selectedBlockIndex', null);
        }
    });
    
    window.addEventListener('beforeunload', function(e) {
        if (Livewire.get('isDirty')) {
            e.preventDefault();
            e.returnValue = '{{ __("pagebuilder::messages.unsaved_changes_warning") }}';
            return '{{ __("pagebuilder::messages.unsaved_changes_warning") }}';
        }
    });
</script>
@endpush