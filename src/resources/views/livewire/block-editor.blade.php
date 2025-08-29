<div>
    <div>
    @if($hasError)
        <!-- Modo de Erro -->
        <div wire:key="block-error-{{ $blockId }}" 
             class="block-editor-error-container mb-4">
            <div class="block-editor-error bg-red-50 border border-red-200 rounded-lg p-4 dark:bg-red-900/20 dark:border-red-800">
                <div class="flex items-center mb-2">
                    <span class="text-red-500 text-lg mr-2 dark:text-red-400">❌</span>
                    <span class="font-medium text-red-800 dark:text-red-300">Erro no Bloco</span>
                </div>
                
                <div class="text-sm text-red-600 mb-2 dark:text-red-400">
                    {{ $errorMessage }}
                </div>
                
                <div class="text-xs text-red-500 dark:text-red-400 mb-3">
                    <div>ID: {{ $blockId }}</div>
                    <div>Tipo: {{ $blockType }}</div>
                    <div>Time: {{ now()->format('H:i:s') }}</div>
                </div>
                
                <div class="flex space-x-2">
                    <button wire:click="remove" 
                            class="px-3 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600 transition-colors dark:bg-red-600 dark:hover:bg-red-700">
                        Remover Bloco
                    </button>
                    
                    <button wire:click="$dispatch('reload-block', { blockId: '{{ $blockId }}' })" 
                            class="px-3 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600 transition-colors dark:bg-blue-600 dark:hover:bg-blue-700">
                        Tentar Novamente
                    </button>
                </div>
            </div>
        </div>
    @else
        <!-- Modo Normal -->
        <div wire:key="block-editor-{{ $blockId }}" 
             class="block-editor-container mb-4 relative"
             x-data="{ isHovering: false }"
             @mouseenter="isHovering = true"
             @mouseleave="isHovering = false">
            
            <!-- Toolbar de Controle -->
            <div class="block-toolbar absolute -top-8 right-0 flex space-x-1 opacity-0 transition-opacity"
                 :class="{ 'opacity-100': isHovering || $isEditing }"
                 x-show="isHovering || $isEditing">
                <button wire:click="startEditing" 
                        class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition-colors"
                        title="Editar Bloco"
                        wire:loading.attr="disabled">
                    ✏️ Editar
                </button>
                <button wire:click="remove" 
                        class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition-colors"
                        title="Remover Bloco"
                        onclick="return confirm('Tem certeza que deseja remover este bloco?')"
                        wire:loading.attr="disabled">
                    🗑️ Remover
                </button>
            </div>

            <!-- Modo de Edição -->
            @if($isEditing)
                <div class="block-editor-mode bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-200 dark:border-gray-600">
                        <div class="flex items-center">
                            <span class="text-xl mr-2">{{ $blockIcon }}</span>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $blockLabel }}</h3>
                        </div>
                        <div class="flex space-x-2">
                            <button wire:click="save" 
                                    wire:loading.attr="disabled"
                                    class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition-colors disabled:opacity-50">
                                💾 Salvar
                            </button>
                            <button wire:click="cancel" 
                                    wire:loading.attr="disabled"
                                    class="px-3 py-1 bg-gray-500 text-white text-sm rounded hover:bg-gray-600 transition-colors disabled:opacity-50">
                                ❌ Cancelar
                            </button>
                        </div>
                    </div>

                    <!-- Erros de Validação -->
                    @if(!empty($validationErrors))
                        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded dark:bg-red-900/20 dark:border-red-800 dark:text-red-300">
                            <strong class="font-bold">Erros de Validação:</strong>
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
                        <div class="p-4 bg-yellow-100 border border-yellow-400 rounded dark:bg-yellow-900/20 dark:border-yellow-800">
                            <p class="text-yellow-800 dark:text-yellow-300">Componente de editor não encontrado para este tipo de bloco.</p>
                            <p class="text-sm text-yellow-600 dark:text-yellow-400">Esperado: {{ $editorComponent }}</p>
                        </div>
                    @endif
                </div>
            @else
                <!-- Modo de Preview -->
                <div class="block-preview-mode cursor-pointer p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-dashed border-gray-300 dark:border-gray-600 hover:border-blue-300 hover:bg-blue-50 dark:hover:border-blue-600 dark:hover:bg-blue-900/20 transition-colors"
                     wire:click="startEditing"
                     wire:loading.attr="disabled">
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
                                <h4 class="font-medium text-gray-800 dark:text-white">{{ $blockLabel }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Clique para editar este bloco</p>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @endif

    <!-- Loading State -->
    <div wire:loading wire:target="startEditing,save,cancel,remove" 
         class="absolute inset-0 bg-white bg-opacity-80 dark:bg-gray-800 dark:bg-opacity-80 rounded-lg flex items-center justify-center">
        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500"></div>
    </div>
</div>

<style>
    .block-editor-container {
        transition: all 0.2s ease;
        position: relative;
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
        position: relative;
    }
    
    .block-editor-error-container {
        animation: shake 0.5s ease-in-out;
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
</style>
</div>