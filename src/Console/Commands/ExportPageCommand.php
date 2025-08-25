<?php

namespace Justino\PageBuilder\Console\Commands;

use Illuminate\Console\Command;
use Justino\PageBuilder\Services\JsonPageStorage;

class ExportPageCommand extends Command
{
    protected $signature = 'pagebuilder:export {slug} {--output=}';
    protected $description = 'Export a page to JSON file';
    
    public function handle()
    {
        $slug = $this->argument('slug');
        $output = $this->option('output') ?? $slug . '.json';
        
        $storage = app(JsonPageStorage::class);
        $page = $storage->find($slug);
        
        if (!$page) {
            $this->error("Page with slug '{$slug}' not found.");
            return 1;
        }
        
        $json = json_encode($page, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($output, $json);
        
        $this->info("Page '{$slug}' exported to '{$output}' successfully.");
        return 0;
    }
}