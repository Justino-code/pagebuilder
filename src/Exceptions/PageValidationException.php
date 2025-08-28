<?php

namespace Justino\PageBuilder\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class PageValidationException extends Exception
{
    protected $validationErrors;
    protected $context;
    
    public function __construct($message = "", $validationErrors = [], $context = [], $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->validationErrors = $validationErrors;
        $this->context = $context;
        
        // Log automático do erro
        Log::error('PageValidationException: ' . $message, array_merge([
            'errors' => $validationErrors
        ], $context));
    }
    
    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }
    
    public function getContext(): array
    {
        return $this->context;
    }
    
    public function getFirstError(): string
    {
        if (!empty($this->validationErrors)) {
            return is_array($this->validationErrors[0]) 
                ? $this->validationErrors[0][0] 
                : $this->validationErrors[0];
        }
        
        return $this->getMessage();
    }
    
    public function toArray(): array
    {
        return [
            'message' => $this->getMessage(),
            'errors' => $this->validationErrors,
            'context' => $this->context,
            'code' => $this->getCode()
        ];
    }
}