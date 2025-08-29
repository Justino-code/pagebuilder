<div class="block-editor-error bg-red-50 border border-red-200 rounded-lg p-4">
    <div class="flex items-center mb-2">
        <span class="text-red-500 text-lg mr-2">❌</span>
        <span class="font-medium text-red-800">Erro no Bloco</span>
    </div>
    
    <div class="text-sm text-red-600 mb-2">
        {{ $errorMessage }}
    </div>
    
    <div class="text-xs text-red-500">
        <div>ID: {{ $blockId }}</div>
        <div>Tipo: {{ $blockType }}</div>
    </div>
    
    <div class="mt-3">
        <button wire:click="remove" 
                class="px-3 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600 transition-colors">
            Remover Bloco
        </button>
    </div>
</div>

<style>
    .block-editor-error {
        animation: shake 0.5s ease-in-out;
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
</style>