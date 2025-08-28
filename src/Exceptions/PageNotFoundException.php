<?php

namespace Justino\PageBuilder\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class PageNotFoundException extends Exception
{
    protected $slug;
    protected $type;
    
    public function __construct($slug, $type = 'page', $message = "", $code = 0, $previous = null)
    {
        $defaultMessage = "Página '{$slug}' do tipo '{$type}' não encontrada.";
        parent::__construct($message ?: $defaultMessage, $code, $previous);
        
        $this->slug = $slug;
        $this->type = $type;
        
        Log::warning('PageNotFoundException: ' . $this->getMessage(), [
            'slug' => $slug,
            'type' => $type
        ]);
    }
    
    public function getSlug(): string
    {
        return $this->slug;
    }
    
    public function getType(): string
    {
        return $this->type;
    }
}