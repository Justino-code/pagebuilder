@extends('pagebuilder::layouts.app')

@section('content')
<div class="h-screen">
    @if(isset($pageData) && isset($pageSlug))
        <livewire:page-builder-editor 
            :pageSlug="$pageSlug" 
            :pageData="$pageData->toArray()" 
        />
    @else
        <livewire:page-builder-editor />
    @endif
</div>
@endsection