<?php

namespace Justino\PageBuilder\Http\Controllers;

use Justino\PageBuilder\Services\JsonPageStorage;
use Justino\PageBuilder\Services\BlockManager;
use Justino\PageBuilder\DTOs\PageData;

class PageController extends Controller
{
    public function show($slug)
    {
        $storage = app(JsonPageStorage::class);
        $blockManager = app(BlockManager::class);
        
        $page = $storage->find($slug, 'page');
        
        if (!$page instanceof PageData || !$page->published) {
            abort(404);
        }
        
        $header = $storage->getDefault('header');
        $footer = $storage->getDefault('footer');
        
        return view('pagebuilder::page', compact('page', 'header', 'footer', 'blockManager'));
    }
    
    public function preview($slug)
    {
        $storage = app(JsonPageStorage::class);
        $blockManager = app(BlockManager::class);
        
        $page = $storage->find($slug, 'page');
        
        if (!$page instanceof PageData) {
            abort(404);
        }
        
        $header = $storage->getDefault('header');
        $footer = $storage->getDefault('footer');
        
        return view('pagebuilder::page', compact('page', 'header', 'footer', 'blockManager'));
    }
}