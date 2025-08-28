<?php
// app/PageBuilder/Events/PageCreated.php

namespace Justino\PageBuilder\Events;

use Justino\PageBuilder\DTOs\PageData;
use Illuminate\Foundation\Events\Dispatchable;

class PageCreated
{
    use Dispatchable;
    
    public $pageData;
    
    public function __construct(PageData $pageData)
    {
        $this->pageData = $pageData;
    }
}