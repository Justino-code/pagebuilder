<div class="form-preview bg-white rounded-lg p-4 border">
    <div class="flex items-center mb-3">
        <span class="text-xl mr-2">📋</span>
        <span class="font-semibold">Contact Form</span>
    </div>
    
    <div class="space-y-2">
        @if(!empty($block['data']['title']))
        <div class="text-sm">
            <strong>Title:</strong> {{ Str::limit($block['data']['title'], 20) }}
        </div>
        @endif
        
        <div class="text-sm">
            <strong>Recipient:</strong> {{ Str::limit($block['data']['email_recipient'] ?? 'Not set', 25) }}
        </div>
        
        <div class="text-sm">
            <strong>Fields:</strong> {{ count($block['data']['fields'] ?? []) }}
            <span class="text-gray-500">
                ({{ collect($block['data']['fields'] ?? [])->where('required', true)->count() }} required)
            </span>
        </div>
        
        @if(!empty($block['data']['fields']))
        <div class="text-xs text-gray-600 mt-2">
            @foreach(array_slice($block['data']['fields'], 0, 3) as $field)
                <div class="truncate">
                    • {{ $field['label'] ?? 'Unlabeled' }} 
                    <span class="text-gray-400">({{ $field['type'] ?? 'text' }})</span>
                </div>
            @endforeach
            @if(count($block['data']['fields']) > 3)
                <div class="text-gray-400">+ {{ count($block['data']['fields']) - 3 }} more fields</div>
            @endif
        </div>
        @endif
    </div>
</div>