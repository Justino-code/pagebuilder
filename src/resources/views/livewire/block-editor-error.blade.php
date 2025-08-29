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