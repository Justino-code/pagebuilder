<div class="space-y-6">
    <div>
        <h3 class="text-lg font-medium mb-4">📏 {{ __('pagebuilder::messages.spacing_settings') }}</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Container Padding -->
            <div>
                <label class="block text-sm font-medium mb-2">
                    {{ __('pagebuilder::messages.container_padding') }}
                </label>
                <select wire:model="styles.spacing.container_padding" class="w-full border rounded px-3 py-2">
                    @foreach($spacingOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Section Spacing -->
            <div>
                <label class="block text-sm font-medium mb-2">
                    {{ __('pagebuilder::messages.section_spacing') }}
                </label>
                <select wire:model="styles.spacing.section_spacing" class="w-full border rounded px-3 py-2">
                    @foreach($spacingOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Element Spacing -->
            <div>
                <label class="block text-sm font-medium mb-2">
                    {{ __('pagebuilder::messages.element_spacing') }}
                </label>
                <select wire:model="styles.spacing.element_spacing" class="w-full border rounded px-3 py-2">
                    @foreach($spacingOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Button Padding -->
            <div>
                <label class="block text-sm font-medium mb-2">
                    {{ __('pagebuilder::messages.button_padding') }}
                </label>
                <select wire:model="styles.spacing.button_padding" class="w-full border rounded px-3 py-2">
                    @foreach($spacingOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Input Padding -->
            <div>
                <label class="block text-sm font-medium mb-2">
                    {{ __('pagebuilder::messages.input_padding') }}
                </label>
                <select wire:model="styles.spacing.input_padding" class="w-full border rounded px-3 py-2">
                    @foreach($spacingOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Preview Section -->
    <div class="mt-8 p-6 border rounded-lg bg-white">
        <h4 class="font-medium mb-4">👀 {{ __('pagebuilder::messages.spacing_preview') }}</h4>
        
        <div class="space-y-4">
            <div class="border-2 border-dashed border-gray-300 p-4 rounded" 
                 style="padding: {{ [
                    '0' => '0', '1' => '0.25rem', '2' => '0.5rem',
                    '3' => '0.75rem', '4' => '1rem', '6' => '1.5rem',
                    '8' => '2rem', '12' => '3rem'
                 ][$styles['spacing']['container_padding']] ?? '1.5rem' }}">
                <div class="text-center text-gray-600">
                    <strong>Container Padding:</strong> {{ $spacingOptions[$styles['spacing']['container_padding']] ?? 'Medium' }}
                </div>
            </div>

            <div class="space-y-2">
                <div class="bg-blue-100 p-2 rounded" 
                     style="margin-bottom: {{ [
                        '0' => '0', '1' => '0.25rem', '2' => '0.5rem',
                        '3' => '0.75rem', '4' => '1rem', '6' => '1.5rem',
                        '8' => '2rem', '12' => '3rem'
                     ][$styles['spacing']['element_spacing']] ?? '1rem' }}">
                    Element 1
                </div>
                <div class="bg-green-100 p-2 rounded">
                    Element 2
                </div>
            </div>

            <button class="bg-blue-500 text-white rounded" 
                    style="padding: {{ [
                        '0' => '0', '1' => '0.25rem', '2' => '0.5rem',
                        '3' => '0.75rem', '4' => '1rem', '6' => '1.5rem'
                     ][$styles['spacing']['button_padding']] ?? '0.75rem' }}">
                Button Example
            </button>
        </div>
    </div>

    <!-- Reset Button -->
    <div class="mt-6">
        <button wire:click="resetStyles('spacing')" 
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
            🔄 {{ __('pagebuilder::messages.reset_spacing') }}
        </button>
    </div>
</div>