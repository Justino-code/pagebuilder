<div wire:key="block-editor-{{ $blockId }}" 
     class="block-editor-container mb-4 relative"
     x-data="{ isHovering: false }"
     @mouseenter="isHovering = true"
     @mouseleave="isHovering = false">
    
    <!-- Toolbar de Controle -->
    <div class="block-toolbar absolute -top-8 right-0 flex space-x-1 opacity-0 transition-opacity"
         :class="{ 'opacity-100': isHovering || $wire.isEditing }"
         x-show="isHovering || $wire.isEditing">
        <button wire:click="startEditing" 
                class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700"
                title="Edit Block">
            ✏️ Edit
        </button>
        <button wire:click="remove" 
                class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700"
                title="Remove Block"
                onclick="return confirm('Are you sure you want to remove this block?')">
            🗑️ Remove
        </button>
    </div>

    <!-- Modo de Edição -->
    @if($isEditing)
        <div class="block-editor-mode bg-white rounded-lg shadow-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between mb-4 pb-2 border-b">
                <div class="flex items-center">
                    <span class="text-xl mr-2">{{ $blockIcon }}</span>
                    <h3 class="text-lg font-semibold text-gray-800">{{ $blockLabel }}</h3>
                </div>
                <div class="flex space-x-2">
                    <button wire:click="save" 
                            class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                        💾 Save
                    </button>
                    <button wire:click="cancel" 
                            class="px-3 py-1 bg-gray-500 text-white text-sm rounded hover:bg-gray-600">
                        ❌ Cancel
                    </button>
                </div>
            </div>

            <!-- Erros de Validação -->
            @if(!empty($validationErrors))
                <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                    <strong class="font-bold">Validation Errors:</strong>
                    <ul class="mt-1 list-disc list-inside text-sm">
                        @foreach($validationErrors as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Componente de Editor Específico -->
            @if(view()->exists($editorComponent))
                @include($editorComponent)
            @else
                <div class="p-4 bg-yellow-100 border border-yellow-400 rounded">
                    <p class="text-yellow-800">Editor component not found for this block type.</p>
                    <p class="text-sm text-yellow-600">Expected: {{ $editorComponent }}</p>
                </div>
            @endif
        </div>
    @else
        <!-- Modo de Preview -->
        <div class="block-preview-mode cursor-pointer p-4 bg-gray-50 rounded-lg border border-dashed hover:border-blue-300 hover:bg-blue-50 transition-colors"
             wire:click="startEditing">
            @if(view()->exists($previewComponent))
                @include($previewComponent, ['block' => [
                    'id' => $blockId,
                    'type' => $blockType,
                    'data' => $blockData,
                    'styles' => $blockStyles
                ]])
            @else
                <div class="flex items-center">
                    <span class="text-xl mr-3">{{ $blockIcon }}</span>
                    <div>
                        <h4 class="font-medium text-gray-800">{{ $blockLabel }}</h4>
                        <p class="text-sm text-gray-600">Click to edit this block</p>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>

<!-- Estilos específicos para o editor -->
<style>
    .block-editor-container {
        transition: all 0.2s ease;
    }
    
    .block-editor-container:hover {
        transform: translateY(-1px);
    }
    
    .block-toolbar {
        z-index: 10;
        transition: opacity 0.2s ease;
    }
    
    .block-preview-mode {
        min-height: 80px;
        display: flex;
        align-items: center;
    }
</style>