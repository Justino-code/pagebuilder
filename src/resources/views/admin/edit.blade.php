@extends('pagebuilder::layouts.app')

@section('content')
<div class="h-screen">
    <livewire:page-builder-editor :pageId="$page->id" />
</div>
@endsection