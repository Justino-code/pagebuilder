<div class="space-y-6">
    <div>
        <h3 class="text-lg font-medium mb-4">🟦 {{ __('pagebuilder::messages.border_settings') }}</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Border Width -->
            <div>
                <label class="block text-sm font-medium mb-2">
                    {{ __('pagebuilder::messages.border_width') }}
                </label>
                <input type="range" wire:model="styles.borders.border_width" min="0" max="8" 
                       class="w-full" value="{{ $styles['borders']['border_width'] }}">
                <div class="text-xs text-gray-600 text-center">
                    {{ $styles['borders']['border_width'] }}px
                </div>
            </div>

            <!-- Border Radius -->
            <div>
                <label class="block text-sm font-medium mb-2">
                    {{ __('pagebuilder::messages.border_radius') }}
                </label>
                <select wire:model="styles.borders.border_radius" class="w-full border rounded px-3 py-2">
                    @foreach($radiusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Border Color -->
            <div>
                <label class="block text-sm font-medium mb-2">
                    {{ __('pagebuilder::messages.border_color') }}
                </label>
                <div class="flex items-center space-x-2">
                    <input type="color" wire:model="styles.borders.border_color" 
                           class="w-10 h-10 border rounded">
                    <input type="text" wire:model="styles.borders.border_color" 
                           class="flex-1 border rounded px-3 py-2 font-mono text-sm">
                </div>
            </div>

            <!-- Border Style -->
            <div>
                <label class="block text-sm font-medium mb-2">
                    {{ __('pagebuilder::messages.border_style') }}
                </label>
                <select wire:model="styles.borders.border_style" class="w-full border rounded px-3 py-2">
                    @foreach($borderStyleOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Preview Section -->
    <div class="mt-8 p-6 border rounded-lg bg-white">
        <h4 class="font-medium mb-4">👀 {{ __('pagebuilder::messages.border_preview') }}</h4>
        
        <div class="space-y-4">
            <div class="p-4 bg-white" 
                 style="border: {{ $styles['borders']['border_width'] }}px {{ $styles['borders']['border_style'] }} {{ $styles['borders']['border_color'] }};
                        border-radius: {{ [
                            'none' => '0', 'sm' => '0.125rem', 'md' => '0.375rem',
                            'lg' => '0.5rem', 'xl' => '0.75rem', 'full' => '9999px'
                         ][$styles['borders']['border_radius']] ?? '0.375rem' }};">
                <p class="text-center">
                    <strong>Border Preview</strong><br>
                    Width: {{ $styles['borders']['border_width'] }}px<br>
                    Style: {{ ucfirst($styles['borders']['border_style']) }}<br>
                    Radius: {{ $radiusOptions[$styles['borders']['border_radius']] ?? 'Medium' }}
                </p>
            </div>

            <button class="px-4 py-2 bg-blue-500 text-white" 
                    style="border-radius: {{ [
                        'none' => '0', 'sm' => '0.125rem', 'md' => '0.375rem',
                        'lg' => '0.5rem', 'xl' => '0.75rem', 'full' => '9999px'
                     ][$styles['borders']['border_radius']] ?? '0.375rem' }};">
                Rounded Button
            </button>

            <input type="text" placeholder="Input field" class="w-full p-2 border" 
                   style="border: {{ $styles['borders']['border_width'] }}px {{ $styles['borders']['border_style'] }} {{ $styles['borders']['border_color'] }};
                          border-radius: {{ [
                              'none' => '0', 'sm' => '0.125rem', 'md' => '0.375rem',
                              'lg' => '0.5rem', 'xl' => '0.75rem', 'full' => '9999px'
                           ][$styles['borders']['border_radius']] ?? '0.375rem' }};">
        </div>
    </div>

    <!-- Reset Button -->
    <div class="mt-6">
        <button wire:click="resetStyles('borders')" 
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
            🔄 {{ __('pagebuilder::messages.reset_borders') }}
        </button>
    </div>
</div>