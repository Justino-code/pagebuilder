@extends('pagebuilder::layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold capitalize">{{ $type }} Templates</h1>
        <a 
            href="{{ route('pagebuilder.template.edit', ['type' => $type]) }}"
            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
        >
            Create New {{ ucfirst($type) }}
        </a>
    </div>

    <livewire:template-manager :type="$type" />
</div>
@endsection