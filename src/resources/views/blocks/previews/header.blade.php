<div class="header-preview bg-white rounded-lg p-4 border">
    <div class="flex items-center mb-3">
        <span class="text-xl mr-2">🔝</span>
        <span class="font-semibold">Header Template</span>
        @if($block['data']['is_default'] ?? false)
        <span class="ml-2 text-xs bg-green-100 text-green-800 px-2 py-1 rounded">
            Default
        </span>
        @endif
    </div>
    
    <div class="space-y-2">
        <div class="text-sm">
            <strong>Name:</strong> {{ $block['data']['name'] ?? 'Unnamed' }}
        </div>
        
        <div class="text-sm">
            <strong>Logo:</strong>
            @if(($block['data']['logo']['type'] ?? 'text') === 'text')
                "{{ $block['data']['logo']['text'] ?? 'No text' }}"
            @else
                @if($block['data']['logo']['image'])
                <span class="text-green-600">Image Logo</span>
                @else
                <span class="text-red-600">No image</span>
                @endif
            @endif
        </div>
        
        <div class="text-sm">
            <strong>Menu Items:</strong> {{ count($block['data']['menu_items'] ?? []) }}
        </div>
        
        <div class="flex flex-wrap gap-1 mt-2">
            @foreach(array_slice($block['data']['menu_items'] ?? [], 0, 4) as $item)
            <span class="text-xs bg-gray-100 px-2 py-1 rounded">
                {{ $item['label'] ?? 'Untitled' }}
            </span>
            @endforeach
            @if(count($block['data']['menu_items'] ?? []) > 4)
            <span class="text-xs text-gray-400">
                +{{ count($block['data']['menu_items']) - 4 }} more
            </span>
            @endif
        </div>
        
        <div class="text-xs text-gray-500 mt-2">
            @if($block['data']['styles']['sticky'] ?? false)
            <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded mr-1">
                Sticky
            </span>
            @endif
            @if(($block['data']['styles']['shadow'] ?? 'none') !== 'none')
            <span class="inline-block bg-purple-100 text-purple-800 px-2 py-1 rounded">
                {{ ucfirst($block['data']['styles']['shadow']) }} shadow
            </span>
            @endif
        </div>
    </div>
</div>