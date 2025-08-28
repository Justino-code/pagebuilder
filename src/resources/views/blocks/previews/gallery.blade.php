<div class="gallery-preview bg-white rounded-lg p-4 border">
    <div class="flex items-center mb-3">
        <span class="text-xl mr-2">🖼️</span>
        <span class="font-semibold">Image Gallery</span>
    </div>
    
    <div class="space-y-2">
        @if(!empty($block['data']['title']))
        <div class="text-sm">
            <strong>Title:</strong> {{ Str::limit($block['data']['title'], 20) }}
        </div>
        @endif
        
        <div class="text-sm">
            <strong>Images:</strong> {{ count($block['data']['images'] ?? []) }}
            <span class="text-gray-500">({{ $block['data']['columns'] ?? 3 }} columns)</span>
        </div>
        
        <div class="text-sm">
            <strong>Settings:</strong>
            <span class="text-xs bg-gray-100 px-2 py-1 rounded ml-1">
                {{ $block['data']['image_aspect_ratio'] ?? '1/1' }}
            </span>
            @if($block['data']['lightbox_enabled'] ?? true)
            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded ml-1">
                Lightbox
            </span>
            @endif
            @if($block['data']['show_captions'] ?? true)
            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded ml-1">
                Captions
            </span>
            @endif
        </div>
        
        @if(!empty($block['data']['images']))
        <div class="mt-3 grid grid-cols-3 gap-1">
            @foreach(array_slice($block['data']['images'], 0, 6) as $image)
                @if($image['image'])
                <div class="aspect-square bg-gray-200 rounded overflow-hidden">
                    <img src="{{ $image['image'] }}" 
                         alt="Preview" 
                         class="w-full h-full object-cover">
                </div>
                @endif
            @endforeach
        </div>
        @endif
    </div>
</div>