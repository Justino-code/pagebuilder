<?php

namespace Justino\PageBuilder;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Justino\PageBuilder\Contracts\StorageInterface;
use Justino\PageBuilder\Services\Storage\FileStorage;
use Justino\PageBuilder\Services\PageBuilderService;
use Justino\PageBuilder\Services\BlockManager;

class PageBuilderServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/resources/views', 'pagebuilder');
        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'pagebuilder');
        
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/pagebuilder.php' => config_path('pagebuilder.php'),
            ], 'pagebuilder-config');
            
            $this->publishes([
                __DIR__.'/resources/views' => resource_path('views/vendor/pagebuilder'),
            ], 'pagebuilder-views');
            
            $this->publishes([
                __DIR__.'/resources/assets' => public_path('vendor/pagebuilder'),
            ], 'pagebuilder-assets');
            
            $this->publishes([
                __DIR__.'/resources/lang' => resource_path('lang/vendor/pagebuilder'),
            ], 'pagebuilder-lang');
        }
        
        $this->registerRoutes();
        $this->registerLivewireComponents();
        $this->registerCustomBlocks();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/pagebuilder.php', 'pagebuilder');
    
        // Registrar implementação de storage
        $this->app->singleton(StorageInterface::class, function ($app) {
            $driver = config('pagebuilder.storage.driver', 'json');
            
            switch ($driver) {
                case 'json':
                case 'file':
                    return new FileStorage();
                // Futuras implementações:
                // case 'database':
                //     return new DatabaseStorage();
                // case 's3':
                //     return new S3Storage();
                default:
                    throw new \InvalidArgumentException("Driver de armazenamento não suportado: {$driver}");
            }
        });
        
        // Registrar serviços
        $this->app->singleton(BlockManager::class, function ($app) {
            return new BlockManager();
        });

        //dd(BlockManager::class);
        
        $this->app->singleton(PageBuilderService::class, function ($app) {
            return new PageBuilderService(
                $app->make(StorageInterface::class),
                $app->make(BlockManager::class)
            );
        });
        
        $this->app->singleton(PageLogger::class, function ($app) {
            return new PageLogger();
        });
        
        // Registrar middleware
        $this->app['router']->aliasMiddleware('pagebuilder.auth', Http\Middleware\PageBuilderAuth::class);

        // Registrar comandos
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Justino\PageBuilder\Console\Commands\InstallCommand::class,
                \Justino\PageBuilder\Console\Commands\PublishTranslations::class,
                \Justino\PageBuilder\Console\Commands\StorageStatsCommand::class,
                \Justino\PageBuilder\Console\Commands\StorageBackupCommand::class,
                \Justino\PageBuilder\Console\Commands\StorageCleanupCommand::class,
            ]);
        }
    }
    
    protected function registerRoutes()
    {
        Route::group([
            'prefix' => config('pagebuilder.route.prefix', 'page-builder'),
            'middleware' => config('pagebuilder.route.middleware', ['web']),
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });
    }
    
    protected function registerLivewireComponents()
    {
        Livewire::component('page-builder-editor', Http\Livewire\PageBuilderEditor::class);
        Livewire::component('page-builder-block', Http\Livewire\PageBuilderBlock::class);
        Livewire::component('media-library', Http\Livewire\MediaLibrary::class);
        Livewire::component('page-manager', Http\Livewire\PageManager::class);
        Livewire::component('template-editor', Http\Livewire\TemplateEditor::class);
        Livewire::component('template-manager', Http\Livewire\TemplateManager::class);
        Livewire::component('language-selector', Http\Livewire\LanguageSelector::class);
        Livewire::component('style-editor', Http\Livewire\AdvancedStyleEditor::class);
        Livewire::component('block-editor', Http\Livewire\BlockEditor::class);
    }

    protected function registerCustomBlocks(): void
    {
        $this->app->booted(function () {
            $blockManager = $this->app->make(BlockManager::class);
            $customBlocks = config('pagebuilder.blocks.custom', []);
            
            foreach ($customBlocks as $blockClass) {
                try {
                    $blockManager->registerBlock($blockClass);
                } catch (\Exception $e) {
                    logger()->error("Failed to register custom block {$blockClass}: " . $e->getMessage());
                }
            }
        });
    }
}