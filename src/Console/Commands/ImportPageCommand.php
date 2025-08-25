<?php

namespace Justino\PageBuilder\Console\Commands;

use Illuminate\Console\Command;
use Justino\PageBuilder\Services\JsonPageStorage;

class ImportPageCommand extends Command
{
    protected $signature = 'pagebuilder:import {file}';
    protected $description = 'Import a page from JSON file';
    
    public function handle()
    {
        $file = $this->argument('file');
        
        if (!file_exists($file)) {
            $this->error("File '{$file}' not found.");
            return 1;
        }
        
        $content = file_get_contents($file);
        $data = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON file: ' . json_last_error_msg());
            return 1;
        }
        
        $storage = app(JsonPageStorage::class);
        $result = $storage->save($data);
        
        if ($result) {
            $slug = $data['slug'] ?? 'unknown';
            $this->info("Page '{$slug}' imported successfully.");
            return 0;
        }
        
        $this->error('Failed to import page.');
        return 1;
    }
}