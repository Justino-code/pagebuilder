<div class="space-y-6">
    <div>
        <h3 class="text-lg font-medium mb-4">✨ {{ __('pagebuilder::messages.effects_settings') }}</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Shadow Intensity -->
            <div>
                <label class="block text-sm font-medium mb-2">
                    {{ __('pagebuilder::messages.shadow_intensity') }}
                </label>
                <select wire:model="styles.effects.shadow_intensity" class="w-full border rounded px-3 py-2">
                    @foreach($shadowOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Hover Effect -->
            <div>
                <label class="block text-sm font-medium mb-2">
                    {{ __('pagebuilder::messages.hover_effect') }}
                </label>
                <select wire:model="styles.effects.hover_effect" class="w-full border rounded px-3 py-2">
                    @foreach($hoverEffects as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Transition Speed -->
            <div>
                <label class="block text-sm font-medium mb-2">
                    {{ __('pagebuilder::messages.transition_speed') }} (ms)
                </label>
                <input type="range" wire:model="styles.effects.transition_speed" min="0" max="1000" 
                       class="w-full" value="{{ $styles['effects']['transition_speed'] }}">
                <div class="text-xs text-gray-600 text-center">
                    {{ $styles['effects']['transition_speed'] }}ms
                </div>
            </div>

            <!-- Opacity -->
            <div>
                <label class="block text-sm font-medium mb-2">
                    {{ __('pagebuilder::messages.opacity') }}
                </label>
                <select wire:model="styles.effects.opacity" class="w-full border rounded px-3 py-2">
                    @foreach($opacityOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Preview Section -->
    <div class="mt-8 p-6 border rounded-lg bg-white">
        <h4 class="font-medium mb-4">👀 {{ __('pagebuilder::messages.effects_preview') }}</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Shadow Preview -->
            <div class="p-4 bg-white rounded" 
                 style="box-shadow: {{ [
                    'none' => 'none',
                    'sm' => '0 1px 2px 0 rgb(0 0 0 / 0.05)',
                    'md' => '0 4px 6px -1px rgb(0 0 0 / 0.1)',
                    'lg' => '0 10px 15px -3px rgb(0 0 0 / 0.1)',
                    'xl' => '0 20px 25px -5px rgb(0 0 0 / 0.1)',
                    '2xl' => '0 25px 50px -12px rgb(0 0 0 / 0.25)'
                 ][$styles['effects']['shadow_intensity']] ?? '0 4px 6px -1px rgb(0 0 0 / 0.1)' }};
                 transition: all {{ $styles['effects']['transition_speed'] }}ms ease-in-out;">
                <p class="text-center">
                    <strong>Shadow: {{ $shadowOptions[$styles['effects']['shadow_intensity']] ?? 'Medium' }}</strong><br>
                    Transition: {{ $styles['effects']['transition_speed'] }}ms
                </p>
            </div>

            <!-- Hover Effect Preview -->
            <div class="p-4 bg-blue-100 rounded text-center 
                 hover-{{ $styles['effects']['hover_effect'] }}"
                 style="transition: all {{ $styles['effects']['transition_speed'] }}ms ease-in-out;
                        opacity: {{ $styles['effects']['opacity'] }}%;">
                <p>
                    <strong>Hover Effect: {{ $hoverEffects[$styles['effects']['hover_effect']] ?? 'Lift' }}</strong><br>
                    Opacity: {{ $styles['effects']['opacity'] }}%<br>
                    <span class="text-sm">(Hover me to see effect)</span>
                </p>
            </div>
        </div>

        <div class="mt-4 p-4 bg-gray-100 rounded">
            <h5 class="font-medium mb-2">Active Classes:</h5>
            <code class="text-sm bg-gray-200 p-1 rounded">
                .shadow-{{ $styles['effects']['shadow_intensity'] }}<br>
                .hover-{{ $styles['effects']['hover_effect'] }}<br>
                .opacity-{{ $styles['effects']['opacity'] }}
            </code>
        </div>
    </div>

    <!-- Reset Button -->
    <div class="mt-6">
        <button wire:click="resetStyles('effects')" 
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
            🔄 {{ __('pagebuilder::messages.reset_effects') }}
        </button>
    </div>
</div>