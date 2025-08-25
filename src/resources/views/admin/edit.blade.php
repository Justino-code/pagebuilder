@extends('pagebuilder::layouts.app')

@section('content')
<div class="h-screen">
    <livewire:page-builder-editor 
        :pageSlug="$pageSlug ?? null" 
        :pageData="$pageData ?? []" 
    />
</div>
@endsection