<?php

namespace Justino\PageBuilder\Http\Controllers;

use Justino\PageBuilder\Services\JsonPageStorage;
use Justino\PageBuilder\Services\BlockManager;

class PageController extends Controller
{
    public function show($slug)
    {
        $storage = app(JsonPageStorage::class);
        $blockManager = app(BlockManager::class);
        
        $page = $storage->find($slug, 'page');
        
        if (!$page || !($page['published'] ?? false)) {
            abort(404, 'Página não encontrada');
        }
        
        // Obter header e footer padrão
        $header = $storage->getDefault('header');
        $footer = $storage->getDefault('footer');
        
        return view('pagebuilder::page', compact('page', 'header', 'footer', 'blockManager'));
    }
    
    public function preview($slug)
    {
        $storage = app(JsonPageStorage::class);
        $blockManager = app(BlockManager::class);
        
        $page = $storage->find($slug, 'page');
        
        if (!$page) {
            abort(404, 'Página não encontrada');
        }
        
        // Para preview, mostramos mesmo não publicado
        $header = $storage->getDefault('header');
        $footer = $storage->getDefault('footer');
        
        return view('pagebuilder::page', compact('page', 'header', 'footer', 'blockManager'));
    }
}