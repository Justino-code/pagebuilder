<div class="field-group select-field mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">
        {{ $field['label'] }}
        @if(($field['required'] ?? false))
            <span class="text-red-500">*</span>
        @endif
    </label>
    
    <select 
        wire:model="{{ $model }}" 
        class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
        @if($field['required'] ?? false) required @endif>
        <option value="">Select {{ $field['label'] }}</option>
        @foreach($field['options'] as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
    </select>
    
    @if(!empty($field['description']))
        <p class="mt-1 text-xs text-gray-500">{{ $field['description'] }}</p>
    @endif
    
    @error($model)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>