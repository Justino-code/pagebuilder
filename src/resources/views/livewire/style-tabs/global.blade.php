<div class="space-y-6">
    <h3 class="text-lg font-semibold">🌍 {{ __('pagebuilder::messages.global_styles') }}</h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Font Family -->
        <div>
            <label class="block text-sm font-medium mb-2">{{ __('pagebuilder::messages.font_family') }}</label>
            <select wire:model="styles.global.font_family" class="w-full p-3 border rounded-lg">
                @foreach($fontOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <!-- Colors -->
        @foreach(['primary_color', 'secondary_color', 'background_color', 'text_color'] as $color)
        <div>
            <label class="block text-sm font-medium mb-2">
                {{ __("pagebuilder::messages.{$color}") }}
            </label>
            <div class="flex items-center space-x-3">
                <input type="color" wire:model="styles.global.{{ $color }}" 
                    class="w-12 h-12 p-1 border rounded cursor-pointer">
                <input type="text" wire:model="styles.global.{{ $color }}" 
                    class="flex-1 p-3 border rounded-lg text-sm" placeholder="#000000">
            </div>
        </div>
        @endforeach
    </div>
</div>