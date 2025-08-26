<div class="advanced-style-editor h-full flex flex-col">
    <!-- Header -->
    <div class="bg-white border-b p-4">
        <h2 class="text-xl font-bold">🎨 {{ __('pagebuilder::messages.advanced_style_editor') }}</h2>
        <p class="text-sm text-gray-600">{{ __('pagebuilder::messages.style_editor_description') }}</p>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex overflow-hidden">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-50 border-r p-4 overflow-auto">
            <nav class="space-y-1">
                <button wire:click="$set('currentTab', 'global')" 
                    class="w-full text-left px-3 py-2 rounded {{ $currentTab === 'global' ? 'bg-blue-100 text-blue-800' : 'text-gray-700 hover:bg-gray-100' }}">
                    🌍 {{ __('pagebuilder::messages.global_styles') }}
                </button>
                <button wire:click="$set('currentTab', 'typography')" 
                    class="w-full text-left px-3 py-2 rounded {{ $currentTab === 'typography' ? 'bg-blue-100 text-blue-800' : 'text-gray-700 hover:bg-gray-100' }}">
                    🔤 {{ __('pagebuilder::messages.typography') }}
                </button>
                <button wire:click="$set('currentTab', 'spacing')" 
                    class="w-full text-left px-3 py-2 rounded {{ $currentTab === 'spacing' ? 'bg-blue-100 text-blue-800' : 'text-gray-700 hover:bg-gray-100' }}">
                    📏 {{ __('pagebuilder::messages.spacing') }}
                </button>
                <button wire:click="$set('currentTab', 'borders')" 
                    class="w-full text-left px-3 py-2 rounded {{ $currentTab === 'borders' ? 'bg-blue-100 text-blue-800' : 'text-gray-700 hover:bg-gray-100' }}">
                    🟦 {{ __('pagebuilder::messages.borders') }}
                </button>
                <button wire:click="$set('currentTab', 'effects')" 
                    class="w-full text-left px-3 py-2 rounded {{ $currentTab === 'effects' ? 'bg-blue-100 text-blue-800' : 'text-gray-700 hover:bg-gray-100' }}">
                    ✨ {{ __('pagebuilder::messages.effects') }}
                </button>
                <button wire:click="$set('currentTab', 'custom')" 
                    class="w-full text-left px-3 py-2 rounded {{ $currentTab === 'custom' ? 'bg-blue-100 text-blue-800' : 'text-gray-700 hover:bg-gray-100' }}">
                    🎯 {{ __('pagebuilder::messages.custom_styles') }}
                </button>
            </nav>

            @if($selectedElement)
            <div class="mt-6 p-3 bg-blue-50 rounded-lg">
                <h4 class="font-medium text-sm">Selected Element:</h4>
                <p class="text-xs text-blue-600">{{ $selectedElement['type'] }}</p>
                <p class="text-xs text-blue-800 font-mono">.{{ $selectedElement['class'] }}</p>
            </div>
            @endif
        </div>

        <!-- Content Area -->
        <div class="flex-1 p-6 overflow-auto">
            @if($currentTab === 'global')
                @include('pagebuilder::livewire.style-tabs.global')
            @elseif($currentTab === 'typography')
                @include('pagebuilder::livewire.style-tabs.typography')
            @elseif($currentTab === 'spacing')
                @include('pagebuilder::livewire.style-tabs.spacing')
            @elseif($currentTab === 'borders')
                @include('pagebuilder::livewire.style-tabs.borders')
            @elseif($currentTab === 'effects')
                @include('pagebuilder::livewire.style-tabs.effects')
            @elseif($currentTab === 'custom')
                @include('pagebuilder::livewire.style-tabs.custom')
            @endif
        </div>
    </div>

    <!-- Preview Panel -->
    <div class="border-t p-4 bg-white">
        <div class="flex justify-between items-center">
            <h4 class="font-medium">👀 {{ __('pagebuilder::messages.live_preview') }}</h4>
            <div class="flex space-x-3">
                <button wire:click="$dispatch('close-style-editor')" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                    {{ __('pagebuilder::messages.cancel') }}
                </button>
                <button wire:click="applyStyles" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    {{ __('pagebuilder::messages.apply_styles') }}
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    {!! $this->generateCss() !!}
</style>