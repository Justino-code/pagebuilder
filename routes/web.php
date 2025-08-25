<?php

use Illuminate\Support\Facades\Route;
use Justino\PageBuilder\Http\Controllers\PageController;

// Rotas de visualização pública
Route::get('/page/{slug}', [PageController::class, 'show'])->name('pagebuilder.page.show');
Route::get('/preview/{slug}', [PageController::class, 'preview'])->name('pagebuilder.page.preview');

// Rotas do admin - todas handled pelo Livewire

    // Listagem de páginas (Livewire)
    Route::get('/', function () {
        return view('pagebuilder::admin.index');
    })->name('pagebuilder.pages.index');
    
    // Criar nova página (Livewire)
    Route::get('/create', function () {
        return view('pagebuilder::admin.editor');
    })->name('pagebuilder.pages.create');
    
    // Editar página existente (Livewire)
    Route::get('/edit/{slug}', function ($slug) {
        $storage = app(\Justino\PageBuilder\Services\JsonPageStorage::class);
        $page = $storage->find($slug, 'page');
        
        if (!$page) {
            abort(404, 'Página não encontrada');
        }
        
        return view('pagebuilder::admin.editor', [
            'pageData' => $page,
            'pageSlug' => $slug
        ]);
    })->name('pagebuilder.pages.edit');
    
    // Templates (Livewire)
    Route::get('/templates/{type}', function ($type) {
        if (!in_array($type, ['header', 'footer'])) {
            abort(404, 'Tipo de template não encontrado');
        }
        
        return view('pagebuilder::admin.templates', compact('type'));
    })->name('pagebuilder.templates.index');
    
    // Editor de templates (Livewire)
    Route::get('/template/{type}/edit/{slug?}', function ($type, $slug = null) {
        if (!in_array($type, ['header', 'footer'])) {
            abort(404, 'Tipo de template não encontrado');
        }
        
        $template = null;
        if ($slug) {
            $storage = app(\Justino\PageBuilder\Services\JsonPageStorage::class);
            $template = $storage->find($slug, $type);
            
            if (!$template) {
                abort(404, 'Template não encontrado');
            }
        }
        
        return view('pagebuilder::admin.template-editor', compact('type', 'slug', 'template'));
    })->name('pagebuilder.templates.edit');