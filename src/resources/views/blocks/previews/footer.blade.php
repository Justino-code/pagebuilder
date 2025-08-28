<div class="footer-preview bg-white rounded-lg p-4 border">
    <div class="flex items-center mb-3">
        <span class="text-xl mr-2">🔻</span>
        <span class="font-semibold">Footer Template</span>
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
            <strong>Sections:</strong> {{ count($block['data']['sections'] ?? []) }}
            <span class="text-gray-500">
                ({{ array_sum(array_map(fn($s) => count($s['links'] ?? []), $block['data']['sections'] ?? [])) }} links)
            </span>
        </div>
        
        <div class="text-sm">
            <strong>Social Links:</strong> {{ count($block['data']['social_links'] ?? []) }}
        </div>
        
        @if(!empty($block['data']['copyright']))
        <div class="text-xs text-gray-600 mt-2">
            {{ Str::limit($block['data']['copyright'], 50) }}
        </div>
        @endif
        
        <div class="flex flex-wrap gap-1 mt-2">
            @foreach($block['data']['sections'] ?? [] as $section)
            <span class="text-xs bg-gray-100 px-2 py-1 rounded">
                {{ $section['title'] ?? 'Untitled' }}
                <span class="text-gray-400">({{ count($section['links'] ?? []) }})</span>
            </span>
            @endforeach
        </div>
    </div>
</div>