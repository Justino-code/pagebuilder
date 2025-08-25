<?php

use Illuminate\Support\Facades\Route;
use Justino\PageBuilder\Http\Controllers\PageController;
use Justino\PageBuilder\Http\Controllers\PageBuilderController;

// Rotas do Page Builder (admin)
Route::get('/', [PageBuilderController::class, 'index'])->name('pagebuilder.pages.index');
Route::get('/create', [PageBuilderController::class, 'create'])->name('pagebuilder.pages.create');
Route::get('/edit/{page}', [PageBuilderController::class, 'edit'])->name('pagebuilder.pages.edit');
Route::post('/store', [PageBuilderController::class, 'store'])->name('pagebuilder.pages.store');
Route::put('/update/{page}', [PageBuilderController::class, 'update'])->name('pagebuilder.pages.update');
Route::delete('/delete/{page}', [PageBuilderController::class, 'destroy'])->name('pagebuilder.pages.destroy');

// Rota para visualizar páginas publicadas
Route::get('/page/{page:slug}', [PageController::class, 'show'])->name('pagebuilder.page.show');


// Rota para processar formulários
Route::post('/form/submit', function (Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'message' => 'required|string',
        'form_type' => 'required|string',
        'recipient' => 'required|email',
    ]);
    
    // Aqui você pode adicionar a lógica para enviar o email
    // Mail::to($validated['recipient'])->send(new ContactFormMail($validated));
    
    return back()->with('success', 'Message sent successfully!');
})->name('pagebuilder.form.submit');

// Rotas para gerenciamento de templates
Route::get('/templates/{type}', function ($type) {
    return view('pagebuilder::admin.templates', ['type' => $type]);
})->name('pagebuilder.templates');

Route::get('/template/edit/{type}/{slug?}', function ($type, $slug = null) {
    return view('pagebuilder::admin.template-editor', [
        'type' => $type,
        'slug' => $slug
    ]);
})->name('pagebuilder.template.edit');