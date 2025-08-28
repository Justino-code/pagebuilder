<div class="block-preview text-preview p-4 bg-gray-50 rounded-lg border border-dashed">
    <div class="flex items-center mb-2">
        <span class="text-lg mr-2">📝</span>
        <span class="font-medium text-gray-700">Text Block</span>
    </div>
    
    <div class="prose max-w-none border-l-4 border-blue-500 pl-3 py-1 bg-white rounded">
        @if(!empty($block['data']['content']))
            {!! Str::limit(strip_tags($block['data']['content']), 150) !!}
        @else
            <p class="text-gray-500 italic">No content yet. Click to edit.</p>
        @endif
    </div>
    
    @if(!empty($block['data']['text_align']) || !empty($block['data']['max_width']))
        <div class="mt-2 text-xs text-gray-500">
            @if(!empty($block['data']['text_align']))
                <span class="inline-block bg-gray-200 rounded px-2 py-1 mr-1">
                    Align: {{ $block['data']['text_align'] }}
                </span>
            @endif
            @if(!empty($block['data']['max_width']) && $block['data']['max_width'] !== 'none')
                <span class="inline-block bg-gray-200 rounded px-2 py-1">
                    Width: {{ $block['data']['max_width'] }}
                </span>
            @endif
        </div>
    @endif
</div>