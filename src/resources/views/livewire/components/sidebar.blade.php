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
                <button wire:click="performAction('saveDraft')" 
                        wire:loading.attr="disabled"
                        class="flex-1 px-3 py-2 bg-gray-600 text-white text-sm rounded hover:bg-gray-700 transition-colors disabled:opacity-50">
                    {{ __('pagebuilder::messages.save_draft') }}
                </button>
                
                <button wire:click="performAction('publish')" 
                        wire:loading.attr="disabled"
                        class="flex-1 px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors disabled:opacity-50">
                    {{ __('pagebuilder::messages.publish') }}
                </button>
            </div>
            
            @if(!$isNew && $published)
            <button wire:click="performAction('unpublish')" 
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
            <button wire:click="performAction('duplicate')" 
                    wire:loading.attr="disabled"
                    class="px-3 py-2 bg-green-500 text-white text-sm rounded hover:bg-green-600 transition-colors disabled:opacity-50">
                {{ __('pagebuilder::messages.duplicate') }}
            </button>
            
            <button wire:click="performAction('showVersionHistory')" 
                    class="px-3 py-2 bg-purple-500 text-white text-sm rounded hover:bg-purple-600 transition-colors">
                {{ __('pagebuilder::messages.versions') }}
            </button>
        </div>
        
        <button wire:click="performAction('clearCache')" 
                class="w-full px-3 py-2 bg-gray-500 text-white text-sm rounded hover:bg-gray-600 transition-colors">
            {{ __('pagebuilder::messages.clear_cache') }}
        </button>
        
        <div class="border-t border-gray-200 dark:border-gray-600 pt-2">
            <button 
                wire:click="performAction('delete')" 
                onclick="return confirm('{{ __('pagebuilder::messages.confirm_delete_page') }}')"
                class="w-full px-3 py-2 bg-red-500 text-white text-sm rounded hover:bg-red-600 transition-colors"
            >
                {{ __('pagebuilder::messages.delete_page') }}
            </button>
        </div>
        @endif
    </div>
</div>