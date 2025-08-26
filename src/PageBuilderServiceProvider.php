<?php

namespace Justino\PageBuilder;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

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
            
            /*$this->publishesMigrations([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'pagebuilder-migrations');*/

            $this->publishes([
                __DIR__.'/resources/lang' => resource_path('lang/vendor/pagebuilder'),
            ], 'pagebuilder-lang');
        }
        
        $this->registerRoutes();
        $this->registerLivewireComponents();
    }

    public function register()
    {
        $this->mergeConfigFrom(
        __DIR__.'/../config/pagebuilder.php', 'pagebuilder');
    
        $this->app->singleton(BlockManager::class, function ($app) {
            return new BlockManager();
        });
        
        $this->app->singleton(JsonPageStorage::class, function ($app) {
            return new JsonPageStorage();
        });
        
        $this->app->singleton(PageLogger::class, function ($app) {
            return new PageLogger();
        });
        
        // Registrar middleware
        $this->app['router']->aliasMiddleware('pagebuilder.auth', Http\Middleware\PageBuilderAuth::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                \Justino\PageBuilder\Console\Commands\InstallCommand::class,
            ]);
        }
    }
    
    protected function registerRoutes()
    {
        Route::group([
            'prefix' => config('pagebuilder.route_prefix', 'page-builder'),
            'middleware' => config('pagebuilder.middleware', ['web']),
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
        Livewire::component('style-editor', \Justino\PageBuilder\Http\Livewire\StyleEditor::class);
    }

}