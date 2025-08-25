<?php

namespace Justino\PageBuilder\Services;

use Illuminate\Support\Facades\Log;

class PageLogger
{
    protected $logChannel;
    
    public function __construct()
    {
        $this->logChannel = config('pagebuilder.logging.channel', 'stack');
    }
    
    public function logPageEdit($pageSlug, $action, $userId = null, $details = [])
    {
        $message = "Page {$action}: {$pageSlug}";
        
        if ($userId) {
            $message .= " by user {$userId}";
        }
        
        Log::channel($this->logChannel)->info($message, $details);
    }
    
    public function logTemplateEdit($templateType, $templateName, $action, $userId = null)
    {
        $message = "{$templateType} template {$action}: {$templateName}";
        
        if ($userId) {
            $message .= " by user {$userId}";
        }
        
        Log::channel($this->logChannel)->info($message);
    }
    
    public function logMediaUpload($filename, $userId = null)
    {
        $message = "Media uploaded: {$filename}";
        
        if ($userId) {
            $message .= " by user {$userId}";
        }
        
        Log::channel($this->logChannel)->info($message);
    }
}