@extends('pagebuilder::layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Pages</h1>
        <a 
            href="{{ route('pagebuilder.pages.create') }}"
            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
        >
            Create New Page
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 text-left">Title</th>
                    <th class="px-4 py-2 text-left">Slug</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pages as $page)
                    <tr class="border-t">
                        <td class="px-4 py-3">{{ $page->title }}</td>
                        <td class="px-4 py-3">{{ $page->slug }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded {{ $page->published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $page->published ? 'Published' : 'Draft' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex space-x-2">
                                <a 
                                    href="{{ route('pagebuilder.pages.edit', $page) }}"
                                    class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600"
                                >
                                    Edit
                                </a>
                                @if($page->published)
                                    <a 
                                        href="{{ $page->url }}"
                                        target="_blank"
                                        class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600"
                                    >
                                        View
                                    </a>
                                @endif
                                <form 
                                    action="{{ route('pagebuilder.pages.destroy', $page) }}"
                                    method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this page?')"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button 
                                        type="submit"
                                        class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600"
                                    >
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                            No pages found. <a href="{{ route('pagebuilder.pages.create') }}" class="text-blue-500 hover:underline">Create your first page</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection