<div class="bg-white dark:bg-gray-800 shadow-sm p-4 border-b border-gray-200 dark:border-gray-700">
    <div class="flex flex-wrap items-center gap-3">
        <!-- Tab Navigation -->
        <button 
            wire:click="switchTab('content')"
            class="px-4 py-2 rounded-lg transition-colors {{ $activeTab === 'content' ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}"
        >
            📝 {{ __('pagebuilder::messages.content') }}
        </button>
        
        <button 
            wire:click="switchTab('style')"
            class="px-4 py-2 rounded-lg transition-colors {{ $activeTab === 'style' ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}"
        >
            🎨 {{ __('pagebuilder::messages.style') }}
        </button>
        
        <button 
            wire:click="switchTab('advanced')"
            class="px-4 py-2 rounded-lg transition-colors {{ $activeTab === 'advanced' ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}"
        >
            ⚙️ {{ __('pagebuilder::messages.advanced') }}
        </button>

        <!-- Action Buttons -->
        <div class="flex-1"></div>

        <button 
            wire:click="performAction('openMediaLibrary')"
            class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
        >
            📁 {{ __('pagebuilder::messages.media_library') }}
        </button>
        
        <button 
            wire:click="performAction('openStyleEditor')"
            class="px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:from-purple-600 hover:to-pink-600 transition-colors"
        >
            🎨 {{ __('pagebuilder::messages.visual_style') }}
        </button>

        <button 
            wire:click="performAction('preview')"
            class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors"
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