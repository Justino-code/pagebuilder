<div class="style-editor space-y-6">
    <h3 class="text-lg font-semibold">{{ __('pagebuilder::messages.style_editor') }}</h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Font Family -->
        <div>
            <label class="block text-sm font-medium mb-2">{{ __('pagebuilder::messages.font_family') }}</label>
            <select wire:model="styles.font_family" class="w-full p-2 border rounded">
                @foreach($fontOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <!-- Primary Color -->
        <div>
            <label class="block text-sm font-medium mb-2">{{ __('pagebuilder::messages.primary_color') }}</label>
            <div class="flex items-center space-x-2">
                <input type="color" wire:model="styles.primary_color" class="w-10 h-10 p-1 border rounded">
                <input type="text" wire:model="styles.primary_color" class="flex-1 p-2 border rounded text-sm">
            </div>
        </div>

        <!-- Secondary Color -->
        <div>
            <label class="block text-sm font-medium mb-2">{{ __('pagebuilder::messages.secondary_color') }}</label>
            <div class="flex items-center space-x-2">
                <input type="color" wire:model="styles.secondary_color" class="w-10 h-10 p-1 border rounded">
                <input type="text" wire:model="styles.secondary_color" class="flex-1 p-2 border rounded text-sm">
            </div>
        </div>

        <!-- Background Color -->
        <div>
            <label class="block text-sm font-medium mb-2">{{ __('pagebuilder::messages.background_color') }}</label>
            <div class="flex items-center space-x-2">
                <input type="color" wire:model="styles.background_color" class="w-10 h-10 p-1 border rounded">
                <input type="text" wire:model="styles.background_color" class="flex-1 p-2 border rounded text-sm">
            </div>
        </div>

        <!-- Text Color -->
        <div>
            <label class="block text-sm font-medium mb-2">{{ __('pagebuilder::messages.text_color') }}</label>
            <div class="flex items-center space-x-2">
                <input type="color" wire:model="styles.text_color" class="w-10 h-10 p-1 border rounded">
                <input type="text" wire:model="styles.text_color" class="flex-1 p-2 border rounded text-sm">
            </div>
        </div>

        <!-- Border Radius -->
        <div>
            <label class="block text-sm font-medium mb-2">{{ __('pagebuilder::messages.border_radius') }}</label>
            <select wire:model="styles.border_radius" class="w-full p-2 border rounded">
                @foreach($borderRadiusOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <!-- Shadow -->
        <div>
            <label class="block text-sm font-medium mb-2">{{ __('pagebuilder::messages.shadow') }}</label>
            <select wire:model="styles.shadow" class="w-full p-2 border rounded">
                @foreach($shadowOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <!-- Spacing -->
        <div>
            <label class="block text-sm font-medium mb-2">{{ __('pagebuilder::messages.spacing') }}</label>
            <select wire:model="styles.spacing" class="w-full p-2 border rounded">
                @foreach($spacingOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Preview -->
    <div class="border rounded-lg p-4 bg-white">
        <h4 class="font-medium mb-3">{{ __('pagebuilder::messages.preview') }}</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-primary text-white p-4 rounded text-center">
                Primary Color
            </div>
            <div class="bg-secondary text-white p-4 rounded text-center">
                Secondary Color
            </div>
            <div class="border border-gray-300 p-4 rounded text-center shadow">
                Card Example
            </div>
        </div>
        <div class="mt-4">
            <button class="bg-primary text-white px-4 py-2 rounded-btn">
                {{ __('pagebuilder::messages.sample_button') }}
            </button>
            <span class="ml-4 text-secondary">{{ __('pagebuilder::messages.sample_text') }}</span>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex justify-end space-x-3">
        <button wire:click="$dispatch('close-style-editor')" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
            {{ __('pagebuilder::messages.cancel') }}
        </button>
        <button wire:click="applyStyles" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
            {{ __('pagebuilder::messages.apply_styles') }}
        </button>
    </div>
</div>

<style>
    {!! $this->generateCss() !!}
</style>