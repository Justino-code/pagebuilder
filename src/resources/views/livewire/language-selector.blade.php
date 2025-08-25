<div class="language-selector">
    <select wire:model="currentLocale" wire:change="changeLocale($event.target.value)" 
            class="border rounded px-2 py-1 text-sm">
        @foreach($availableLocales as $code => $name)
            <option value="{{ $code }}" {{ $currentLocale == $code ? 'selected' : '' }}>
                {{ $name }}
            </option>
        @endforeach
    </select>
</div>