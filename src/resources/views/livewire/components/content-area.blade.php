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
                        <button wire:click="$dispatch('exportRequested')" 
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
                            <input type="file" wire:change="$dispatch('importFileSelected', { file: $event.target.files[0] })" class="hidden" accept=".json">
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