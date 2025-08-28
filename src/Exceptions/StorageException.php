<?php

namespace Justino\PageBuilder\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class StorageException extends Exception
{
    protected $operation;
    protected $filePath;
    
    public function __construct($operation, $filePath = null, $message = "", $code = 0, $previous = null)
    {
        $defaultMessage = "Erro durante a operação '{$operation}'";
        if ($filePath) {
            $defaultMessage .= " no arquivo '{$filePath}'";
        }
        
        parent::__construct($message ?: $defaultMessage, $code, $previous);
        
        $this->operation = $operation;
        $this->filePath = $filePath;
        
        Log::error('StorageException: ' . $this->getMessage(), [
            'operation' => $operation,
            'file_path' => $filePath
        ]);
    }
    
    public function getOperation(): string
    {
        return $this->operation;
    }
    
    public function getFilePath(): ?string
    {
        return $this->filePath;
    }
}