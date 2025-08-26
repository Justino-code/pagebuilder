<div class="space-y-6">
    <h3 class="text-lg font-semibold">🎯 {{ __('pagebuilder::messages.custom_styles') }}</h3>
    
    @if($selectedElement)
    <div class="bg-blue-50 p-4 rounded-lg">
        <h4 class="font-medium mb-2">Editing: .{{ $selectedElement['class'] }}</h4>
        <p class="text-sm text-blue-600">Add custom CSS properties for this element</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Custom Class Name -->
        <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-2">Custom Class Name</label>
            <input type="text" wire:model="customClassName" 
                placeholder="e.g., my-custom-button" 
                class="w-full p-3 border rounded-lg">
        </div>

        <!-- CSS Properties -->
        @foreach([
            'color' => 'Color', 'background-color' => 'Background', 
            'font-size' => 'Font Size', 'font-weight' => 'Font Weight',
            'padding' => 'Padding', 'margin' => 'Margin',
            'border' => 'Border', 'border-radius' => 'Border Radius'
        ] as $property => $label)
        <div>
            <label class="block text-sm font-medium mb-2">{{ $label }}</label>
            <input type="text" wire:model="customStyles.{{ $property }}" 
                placeholder="{{ $property }}"
                class="w-full p-3 border rounded-lg text-sm">
        </div>
        @endforeach
    </div>

    <button wire:click="addCustomClass" 
        class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
        ➕ Add Custom Class
    </button>

    @else
    <div class="text-center py-12 text-gray-500">
        <div class="text-4xl mb-4">🎯</div>
        <p>{{ __('pagebuilder::messages.select_element_to_style') }}</p>
        <p class="text-sm mt-2">{{ __('pagebuilder::messages.click_element_to_start') }}</p>
    </div>
    @endif

    <!-- Existing Custom Classes -->
    @if(!empty($styles['custom']))
    <div class="mt-8">
        <h4 class="font-medium mb-3">Existing Custom Classes</h4>
        <div class="space-y-3">
            @foreach($styles['custom'] as $className => $classStyles)
            <div class="border rounded-lg p-3 bg-gray-50">
                <div class="flex justify-between items-center mb-2">
                    <span class="font-mono text-sm">.{{ $className }}</span>
                    <button wire:click="$delete('styles.custom.{{ $className }}')" 
                        class="text-red-500 hover:text-red-700">
                        🗑️
                    </button>
                </div>
                <div class="text-xs text-gray-600">
                    @foreach($classStyles as $prop => $value)
                    <div>{{ $prop }}: {{ $value }}</div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>