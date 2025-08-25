<?php

namespace Justino\PageBuilder\Console\Commands;

use Illuminate\Console\Command;

class PublishTranslations extends Command
{
    protected $signature = 'pagebuilder:translations {--force : Overwrite existing files}';
    protected $description = 'Publish Page Builder translation files';
    
    public function handle()
    {
        $this->call('vendor:publish', [
            '--provider' => 'Justino\PageBuilder\PageBuilderServiceProvider',
            '--tag' => 'pagebuilder-lang',
            '--force' => $this->option('force'),
        ]);
        
        $this->info('Translation files published successfully.');
    }
}