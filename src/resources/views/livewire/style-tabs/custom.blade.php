<div class="space-y-6">
    <div>
        <h3 class="text-lg font-medium mb-4">🎯 {{ __('pagebuilder::messages.custom_styles') }}</h3>
        
        @if($selectedElement)
        <div class="bg-blue-50 p-4 rounded-lg mb-4">
            <h4 class="font-medium text-blue-800">Selected Element:</h4>
            <p class="text-sm text-blue-600">{{ $selectedElement['type'] }}</p>
            <code class="text-xs text-blue-800">.{{ $selectedElement['class'] }}</code>
        </div>
        @endif

        <!-- Custom Class Name -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">
                {{ __('pagebuilder::messages.custom_class_name') }}
            </label>
            <input type="text" wire:model="customClassName" 
                   placeholder="e.g., my-custom-button"
                   class="w-full border rounded px-3 py-2">
        </div>

        <!-- Custom CSS Editor -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">
                {{ __('pagebuilder::messages.custom_css') }}
            </label>
            <textarea wire:model="customCss" rows="8" 
                      placeholder="/* Add your custom CSS here */"
                      class="w-full border rounded px-3 py-2 font-mono text-sm"></textarea>
        </div>

        <!-- Add Custom Class Button -->
        @if($selectedElement && $customClassName)
        <button wire:click="addCustomClass" 
                class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
            ➕ {{ __('pagebuilder::messages.add_custom_class') }}
        </button>
        @endif
    </div>

    <!-- Custom Classes List -->
    @if(!empty($styles['custom']))
    <div>
        <h4 class="font-medium mb-4">📋 {{ __('pagebuilder::messages.custom_classes') }}</h4>
        
        <div class="space-y-3">
            @foreach($styles['custom'] as $className => $stylesArray)
            <div class="border rounded p-3 bg-gray-50">
                <div class="flex justify-between items-center mb-2">
                    <span class="font-mono text-sm bg-yellow-100 px-2 py-1 rounded">.{{ $className }}</span>
                    <button wire:click="removeCustomClass('{{ $className }}')" 
                            class="text-red-500 hover:text-red-700">
                        🗑️
                    </button>
                </div>
                
                <div class="text-xs text-gray-600">
                    @foreach($stylesArray as $property => $value)
                    <div class="flex justify-between">
                        <span class="font-mono">{{ $property }}:</span>
                        <span class="font-mono">{{ $value }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- CSS Preview -->
    <div class="mt-6 p-4 border rounded-lg bg-gray-50">
        <h4 class="font-medium mb-2">📝 {{ __('pagebuilder::messages.generated_css') }}</h4>
        <pre class="bg-white p-3 rounded text-xs font-mono overflow-auto max-h-40">{{ $this->generateCss() }}</pre>
    </div>

    <!-- Reset Button -->
    <div class="mt-6">
        <button wire:click="resetStyles('custom')" 
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
            🔄 {{ __('pagebuilder::messages.reset_custom') }}
        </button>
    </div>
</div>