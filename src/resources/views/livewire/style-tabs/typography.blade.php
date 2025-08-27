<div class="space-y-6">
    <div>
        <h3 class="text-lg font-medium mb-4">🔤 {{ __('pagebuilder::messages.typography_settings') }}</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Heading Size -->
            <div>
                <label class="block text-sm font-medium mb-2">
                    {{ __('pagebuilder::messages.heading_size') }}
                </label>
                <select wire:model="styles.typography.heading_size" class="w-full border rounded px-3 py-2">
                    @foreach($sizeOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Body Size -->
            <div>
                <label class="block text-sm font-medium mb-2">
                    {{ __('pagebuilder::messages.body_size') }}
                </label>
                <select wire:model="styles.typography.body_size" class="w-full border rounded px-3 py-2">
                    @foreach($sizeOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Font Weight -->
            <div>
                <label class="block text-sm font-medium mb-2">
                    {{ __('pagebuilder::messages.font_weight') }}
                </label>
                <select wire:model="styles.typography.font_weight" class="w-full border rounded px-3 py-2">
                    @foreach($fontWeightOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Line Height -->
            <div>
                <label class="block text-sm font-medium mb-2">
                    {{ __('pagebuilder::messages.line_height') }}
                </label>
                <select wire:model="styles.typography.line_height" class="w-full border rounded px-3 py-2">
                    <option value="tight">Tight</option>
                    <option value="normal">Normal</option>
                    <option value="relaxed">Relaxed</option>
                    <option value="loose">Loose</option>
                </select>
            </div>

            <!-- Letter Spacing -->
            <div>
                <label class="block text-sm font-medium mb-2">
                    {{ __('pagebuilder::messages.letter_spacing') }}
                </label>
                <select wire:model="styles.typography.letter_spacing" class="w-full border rounded px-3 py-2">
                    <option value="tighter">Tighter</option>
                    <option value="tight">Tight</option>
                    <option value="normal">Normal</option>
                    <option value="wide">Wide</option>
                    <option value="wider">Wider</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Preview Section -->
    <div class="mt-8 p-6 border rounded-lg bg-white">
        <h4 class="font-medium mb-4">👀 {{ __('pagebuilder::messages.typography_preview') }}</h4>
        
        <div class="space-y-4">
            <h1 class="font-bold" style="font-size: {{ [
                'xs' => '0.75rem', 'sm' => '0.875rem', 'base' => '1rem',
                'lg' => '1.125rem', 'xl' => '1.25rem', '2xl' => '1.5rem',
                '3xl' => '1.875rem', '4xl' => '2.25rem'
            ][$styles['typography']['heading_size']] ?? '1.5rem' }}">
                {{ __('pagebuilder::messages.heading_example') }}
            </h1>
            
            <p style="font-size: {{ [
                'xs' => '0.75rem', 'sm' => '0.875rem', 'base' => '1rem',
                'lg' => '1.125rem', 'xl' => '1.25rem', '2xl' => '1.5rem'
            ][$styles['typography']['body_size']] ?? '1rem' }}">
                {{ __('pagebuilder::messages.paragraph_example') }}
            </p>
            
            <div class="text-sm text-gray-600">
                <strong>Font Weight:</strong> {{ $fontWeightOptions[$styles['typography']['font_weight']] ?? 'Normal' }}<br>
                <strong>Line Height:</strong> {{ ucfirst($styles['typography']['line_height']) }}<br>
                <strong>Letter Spacing:</strong> {{ ucfirst($styles['typography']['letter_spacing']) }}
            </div>
        </div>
    </div>

    <!-- Reset Button -->
    <div class="mt-6">
        <button wire:click="resetStyles('typography')" 
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
            🔄 {{ __('pagebuilder::messages.reset_typography') }}
        </button>
    </div>
</div>