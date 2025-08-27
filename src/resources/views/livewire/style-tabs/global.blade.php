<div class="space-y-6">
    <div>
        <h3 class="text-lg font-medium mb-4">🌍 {{ __('pagebuilder::messages.global_settings') }}</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Font Family -->
            <div>
                <label class="block text-sm font-medium mb-2">
                    {{ __('pagebuilder::messages.font_family') }}
                </label>
                <select wire:model="styles.global.font_family" class="w-full border rounded px-3 py-2">
                    @foreach($fontOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Primary Color -->
            <div>
                <label class="block text-sm font-medium mb-2">
                    {{ __('pagebuilder::messages.primary_color') }}
                </label>
                <div class="flex items-center space-x-2">
                    <input type="color" wire:model="styles.global.primary_color" 
                           class="w-10 h-10 border rounded">
                    <input type="text" wire:model="styles.global.primary_color" 
                           class="flex-1 border rounded px-3 py-2 font-mono text-sm">
                </div>
            </div>

            <!-- Secondary Color -->
            <div>
                <label class="block text-sm font-medium mb-2">
                    {{ __('pagebuilder::messages.secondary_color') }}
                </label>
                <div class="flex items-center space-x-2">
                    <input type="color" wire:model="styles.global.secondary_color" 
                           class="w-10 h-10 border rounded">
                    <input type="text" wire:model="styles.global.secondary_color" 
                           class="flex-1 border rounded px-3 py-2 font-mono text-sm">
                </div>
            </div>

            <!-- Background Color -->
            <div>
                <label class="block text-sm font-medium mb-2">
                    {{ __('pagebuilder::messages.background_color') }}
                </label>
                <div class="flex items-center space-x-2">
                    <input type="color" wire:model="styles.global.background_color" 
                           class="w-10 h-10 border rounded">
                    <input type="text" wire:model="styles.global.background_color" 
                           class="flex-1 border rounded px-3 py-2 font-mono text-sm">
                </div>
            </div>

            <!-- Text Color -->
            <div>
                <label class="block text-sm font-medium mb-2">
                    {{ __('pagebuilder::messages.text_color') }}
                </label>
                <div class="flex items-center space-x-2">
                    <input type="color" wire:model="styles.global.text_color" 
                           class="w-10 h-10 border rounded">
                    <input type="text" wire:model="styles.global.text_color" 
                           class="flex-1 border rounded px-3 py-2 font-mono text-sm">
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Section -->
    <div class="mt-8 p-6 border rounded-lg bg-white">
        <h4 class="font-medium mb-4">👀 {{ __('pagebuilder::messages.preview') }}</h4>
        
        <div class="space-y-4">
            <div class="text-2xl font-bold" style="color: {{ $styles['global']['primary_color'] }}">
                {{ __('pagebuilder::messages.heading_example') }}
            </div>
            
            <p style="color: {{ $styles['global']['text_color'] }}">
                {{ __('pagebuilder::messages.paragraph_example') }}
            </p>
            
            <button class="px-4 py-2 rounded text-white" 
                    style="background-color: {{ $styles['global']['primary_color'] }}">
                {{ __('pagebuilder::messages.button_example') }}
            </button>
        </div>
    </div>

    <!-- Reset Button -->
    <div class="mt-6">
        <button wire:click="resetStyles('global')" 
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
            🔄 {{ __('pagebuilder::messages.reset_global_styles') }}
        </button>
    </div>
</div>