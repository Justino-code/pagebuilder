<?php

namespace Justino\PageBuilder\Http\Controllers;

use Illuminate\Http\Request;
use Justino\PageBuilder\Models\Page;

class PageBuilderController extends Controller
{
    public function index()
    {
        $pages = Page::all();
        return view('pagebuilder::admin.index', compact('pages'));
    }
    
    public function create()
    {
        return view('pagebuilder::admin.create');
    }
    
    public function edit(Page $page)
    {
        return view('pagebuilder::admin.edit', compact('page'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|alpha_dash|unique:pages,slug',
        ]);
        
        $page = Page::create($validated);
        
        return redirect()->route('pagebuilder.pages.edit', $page);
    }
    
    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|alpha_dash|unique:pages,slug,' . $page->id,
            'content' => 'sometimes|array',
            'published' => 'boolean',
            'custom_css' => 'nullable|string',
            'custom_js' => 'nullable|string',
        ]);
        
        $page->update($validated);
        
        return response()->json(['message' => 'Page updated successfully']);
    }
    
    public function destroy(Page $page)
    {
        $page->delete();
        
        return redirect()->route('pagebuilder.pages.index')
            ->with('success', 'Page deleted successfully');
    }
}