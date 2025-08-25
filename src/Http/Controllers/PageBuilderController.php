<?php

namespace Justino\PageBuilder\Http\Controllers;

use Illuminate\Http\Request;
use Justino\PageBuilder\Services\JsonPageStorage;
use Justino\PageBuilder\Helpers\Translator;

class PageBuilderController extends Controller
{
    protected $storage;
    
    public function __construct(JsonPageStorage $storage)
    {
        $this->storage = $storage;
    }
    
    public function index()
    {
        $pages = $this->storage->all('page');
        return view('pagebuilder::admin.index', compact('pages'));
    }
    
    public function create()
    {
        return view('pagebuilder::admin.create');
    }
    
    public function edit($slug)
    {
        $page = $this->storage->find($slug, 'page');
        
        if (!$page) {
            abort(404, Translator::trans('page_not_found'));
        }
        
        return view('pagebuilder::admin.edit', compact('page', 'slug'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|alpha_dash|unique_page_slug',
        ], [
            'unique_page_slug' => Translator::trans('slug_already_exists')
        ]);
        
        // Verificar se slug já existe
        if ($this->storage->find($validated['slug'], 'page')) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['slug' => Translator::trans('slug_already_exists')]);
        }
        
        $pageData = [
            'type' => 'page',
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'content' => [],
            'published' => false,
            'header_enabled' => true,
            'footer_enabled' => true,
            'custom_css' => '',
            'custom_js' => '',
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString()
        ];
        
        $this->storage->save($pageData);
        
        return redirect()->route('pagebuilder.pages.edit', $validated['slug'])
            ->with('success', Translator::trans('page_created'));
    }
    
    public function update(Request $request, $slug)
    {
        $page = $this->storage->find($slug, 'page');
        
        if (!$page) {
            return response()->json([
                'error' => Translator::trans('page_not_found')
            ], 404);
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|alpha_dash',
            'content' => 'sometimes|array',
            'published' => 'boolean',
            'custom_css' => 'nullable|string',
            'custom_js' => 'nullable|string',
            'header_enabled' => 'boolean',
            'footer_enabled' => 'boolean',
        ]);
        
        // Se o slug mudou, verificar se já existe
        if ($validated['slug'] !== $slug) {
            if ($this->storage->find($validated['slug'], 'page')) {
                return response()->json([
                    'error' => Translator::trans('slug_already_exists')
                ], 422);
            }
            
            // Remover arquivo antigo
            $this->storage->delete($slug);
        }
        
        $pageData = array_merge($page, $validated, [
            'updated_at' => now()->toISOString()
        ]);
        
        $this->storage->save($pageData);
        
        return response()->json([
            'message' => Translator::trans('page_updated'),
            'slug' => $validated['slug']
        ]);
    }
    
    public function destroy($slug)
    {
        $page = $this->storage->find($slug, 'page');
        
        if (!$page) {
            return redirect()->route('pagebuilder.pages.index')
                ->with('error', Translator::trans('page_not_found'));
        }
        
        $this->storage->delete($slug);
        
        return redirect()->route('pagebuilder.pages.index')
            ->with('success', Translator::trans('page_deleted'));
    }
    
    public function publish($slug)
    {
        $page = $this->storage->find($slug, 'page');
        
        if (!$page) {
            return response()->json([
                'error' => Translator::trans('page_not_found')
            ], 404);
        }
        
        $page['published'] = true;
        $page['updated_at'] = now()->toISOString();
        
        $this->storage->save($page);
        
        return response()->json([
            'message' => Translator::trans('page_published')
        ]);
    }
    
    public function unpublish($slug)
    {
        $page = $this->storage->find($slug, 'page');
        
        if (!$page) {
            return response()->json([
                'error' => Translator::trans('page_not_found')
            ], 404);
        }
        
        $page['published'] = false;
        $page['updated_at'] = now()->toISOString();
        
        $this->storage->save($page);
        
        return response()->json([
            'message' => Translator::trans('page_unpublished')
        ]);
    }
}