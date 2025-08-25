<?php

namespace Justino\PageBuilder\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    protected $signature = 'pagebuilder:install';
    protected $description = 'Install the Page Builder package';
    
    public function handle()
    {
        $this->info('Installing Page Builder...');
        
        // Publicar configurações
        $this->call('vendor:publish', [
            '--provider' => 'Justino\PageBuilder\PageBuilderServiceProvider',
            '--tag' => 'pagebuilder-config'
        ]);
        
        // Publicar views
        $this->call('vendor:publish', [
            '--provider' => 'Justino\PageBuilder\PageBuilderServiceProvider',
            '--tag' => 'pagebuilder-views'
        ]);
        
        // Publicar assets
        $this->call('vendor:publish', [
            '--provider' => 'Justino\PageBuilder\PageBuilderServiceProvider',
            '--tag' => 'pagebuilder-assets'
        ]);
        
        // Criar diretórios de armazenamento
        $storagePath = config('pagebuilder.storage.path', storage_path('app/pagebuilder'));
        if (!File::exists($storagePath)) {
            File::makeDirectory($storagePath, 0755, true);
            File::makeDirectory($storagePath . '/media', 0755, true);
        }

        // Detectar host/porta atuais do artisan serve
        $host = $_SERVER['SERVER_NAME'] ?? '127.0.0.1';
        $port = $_SERVER['SERVER_PORT'] ?? 8000;

        $baseUrl = "http://{$host}:{$port}/" . trim(config('pagebuilder.route.prefix', 'page-builder'), '/');

        $this->info('Page Builder installed successfully!');
        $this->line("You can now access the page builder at: {$baseUrl}");
    }
}
