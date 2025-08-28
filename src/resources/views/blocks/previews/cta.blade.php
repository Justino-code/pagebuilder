<div class="cta-preview bg-gray-100 rounded-lg p-4">
    <div class="flex items-center mb-3">
        <span class="text-xl mr-2">📢</span>
        <span class="font-semibold">Call to Action</span>
    </div>
    
    <div class="space-y-2">
        <div class="text-sm">
            <strong>Title:</strong> {{ Str::limit($block['data']['title'] ?? 'No title', 25) }}
        </div>
        
        @if(!empty($block['data']['description']))
        <div class="text-sm text-gray-600">
            {{ Str::limit($block['data']['description'], 30) }}
        </div>
        @endif
        
        @if(!empty($block['data']['button_text']))
        <div class="text-sm">
            <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">
                Button: {{ $block['data']['button_text'] }}
            </span>
        </div>
        @endif
        
        <div class="text-xs text-gray-500">
            Layout: {{ $block['data']['layout'] ?? 'centered' }}
        </div>
    </div>
</div>