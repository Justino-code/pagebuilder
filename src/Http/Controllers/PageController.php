<?php

namespace Justino\PageBuilder\Http\Controllers;

use Justino\PageBuilder\Models\Page;

class PageController extends Controller
{
    public function show(Page $page)
    {
        if (!$page->published) {
            abort(404);
        }
        
        return view('pagebuilder::page', compact('page'));
    }
}