@extends('pagebuilder::layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">
        {{ $slug ? 'Edit' : 'Create' }} {{ ucfirst($type) }} Template
    </h1>

    @php
        $blockManager = app('Justino\PageBuilder\Services\BlockManager');
        $blockClass = $blockManager->getBlockClass($type);
    @endphp

    @if($blockClass)
        <livewire:page-builder-block-editor 
            :type="$type"
            :slug="$slug"
            :blockSchema="$blockClass::schema()"
            :defaultData="$blockClass::defaults()"
        />
    @else
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            Invalid template type: {{ $type }}
        </div>
    @endif
</div>
@endsection