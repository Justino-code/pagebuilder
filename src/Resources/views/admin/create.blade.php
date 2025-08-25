@extends('pagebuilder::layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Create New Page</h1>

    <form action="{{ route('pagebuilder.pages.store') }}" method="POST" class="max-w-lg">
        @csrf
        
        <div class="mb-4">
            <label for="title" class="block text-sm font-medium mb-1">Page Title</label>
            <input 
                type="text" 
                name="title" 
                id="title"
                required
                class="w-full p-2 border rounded"
                placeholder="Enter page title"
            >
        </div>
        
        <div class="mb-4">
            <label for="slug" class="block text-sm font-medium mb-1">Slug</label>
            <input 
                type="text" 
                name="slug" 
                id="slug"
                required
                class="w-full p-2 border rounded"
                placeholder="page-url"
            >
            <p class="text-xs text-gray-500 mt-1">This will be used in the page URL</p>
        </div>
        
        <div class="flex space-x-2">
            <button 
                type="submit"
                class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
            >
                Create Page
            </button>
            <a 
                href="{{ route('pagebuilder.pages.index') }}"
                class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300"
            >
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection