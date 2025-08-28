<div class="hero-preview bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg p-4 text-white">
    <div class="flex items-center mb-3">
        <span class="text-xl mr-2">📱</span>
        <span class="font-semibold">Hero Section</span>
    </div>
    
    <div class="space-y-2">
        <div class="text-sm">
            <strong>Title:</strong> {{ Str::limit($block['data']['title'] ?? 'No title', 30) }}
        </div>
        
        @if(!empty($block['data']['subtitle']))
        <div class="text-sm opacity-90">
            <strong>Subtitle:</strong> {{ Str::limit($block['data']['subtitle'], 40) }}
        </div>
        @endif
        
        @if(!empty($block['data']['cta_text']))
        <div class="text-sm">
            <span class="inline-block bg-white text-blue-600 px-2 py-1 rounded text-xs">
                CTA: {{ $block['data']['cta_text'] }}
            </span>
        </div>
        @endif
        
        @if(!empty($block['data']['background_image']))
        <div class="text-xs opacity-75">
            Has background image
        </div>
        @endif
    </div>
</div>