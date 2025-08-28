<div class="cards-preview bg-white rounded-lg p-4 border">
    <div class="flex items-center mb-3">
        <span class="text-xl mr-2">🃏</span>
        <span class="font-semibold">Cards Grid</span>
    </div>
    
    <div class="space-y-2">
        @if(!empty($block['data']['title']))
        <div class="text-sm">
            <strong>Title:</strong> {{ Str::limit($block['data']['title'], 20) }}
        </div>
        @endif
        
        <div class="text-sm">
            <strong>Cards:</strong> {{ count($block['data']['cards'] ?? []) }}
            <span class="text-gray-500">({{ $block['data']['columns'] ?? 3 }} columns)</span>
        </div>
        
        @if(!empty($block['data']['cards']))
        <div class="text-xs text-gray-600 mt-2">
            @foreach(array_slice($block['data']['cards'], 0, 3) as $card)
                <div class="truncate">• {{ $card['title'] ?? 'Untitled' }}</div>
            @endforeach
            @if(count($block['data']['cards']) > 3)
                <div class="text-gray-400">+ {{ count($block['data']['cards']) - 3 }} more</div>
            @endif
        </div>
        @endif
    </div>
</div>