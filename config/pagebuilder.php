<?php

return [
    // Configurações básicas
     'storage' => [
        'driver' => env('PAGEBUILDER_STORAGE_DRIVER', 'file'),
        'path' => storage_path('app/pagebuilder'),
        'max_revisions' => env('PAGEBUILDER_MAX_REVISIONS', 10),
        'backup' => [
            'enabled' => env('PAGEBUILDER_BACKUP_ENABLED', true),
            'retention_days' => env('PAGEBUILDER_BACKUP_RETENTION_DAYS', 30),
        ],
        'cleanup' => [
            'auto_cleanup' => env('PAGEBUILDER_AUTO_CLEANUP', false),
            'cleanup_schedule' => env('PAGEBUILDER_CLEANUP_SCHEDULE', '0 2 * * *'), // Daily at 2 AM
        ],
    ],
    
    'media' => [
        'disk' => env('PAGEBUILDER_MEDIA_DISK', 'public'),
        'path' => 'pagebuilder/media',
        'max_file_size' => 2048, // KB
        'allowed_mime_types' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'],
    ],
    
    'route' => [
        'prefix' => env('PAGEBUILDER_ROUTE_PREFIX', 'page-builder'),
        'middleware' => ['web', 'pagebuilder.auth'],
    ],
    
    'frontend_route' => [
        'prefix' => env('PAGEBUILDER_FRONTEND_PREFIX', 'page'),
        'middleware' => ['web'],
    ],
    
    'auth' => [
        'enabled' => env('PAGEBUILDER_AUTH_ENABLED', true),
        'roles' => explode(',', env('PAGEBUILDER_AUTH_ROLES', 'admin')),
    ],
    
    'ui' => [
        'css_framework' => env('PAGEBUILDER_CSS_FRAMEWORK', 'tailwind'),
        'theme' => env('PAGEBUILDER_THEME', 'light'),
        'default_theme' => env('PAGEBUILDER_DEFAULT_THEME', 'system'), // 'light', 'dark', 'system'
    ],
    
    'logging' => [
        'enabled' => env('PAGEBUILDER_LOGGING_ENABLED', true),
        'channel' => env('PAGEBUILDER_LOGGING_CHANNEL', 'stack'),
    ],
    
    'blocks' => [
        'default' => [
            \Justino\PageBuilder\Blocks\HeroBlock::class,
            \Justino\PageBuilder\Blocks\TextBlock::class,
            \Justino\PageBuilder\Blocks\CTABlock::class,
            \Justino\PageBuilder\Blocks\CardsBlock::class,
            \Justino\PageBuilder\Blocks\GalleryBlock::class,
            \Justino\PageBuilder\Blocks\FormBlock::class,
            \Justino\PageBuilder\Blocks\HeaderBlock::class,
            \Justino\PageBuilder\Blocks\FooterBlock::class,
        ],
    ],
    
    'export' => [
        'include_media' => env('PAGEBUILDER_EXPORT_INCLUDE_MEDIA', false),
        'format' => env('PAGEBUILDER_EXPORT_FORMAT', 'json'),
    ],

    'localization' => [
        'enabled' => env('PAGEBUILDER_I18N_ENABLED', true),
        'default_locale' => env('PAGEBUILDER_DEFAULT_LOCALE', 'pt'),
        'available_locales' => ['en', 'pt', 'es'],
        'auto_detect' => env('PAGEBUILDER_AUTO_DETECT_LOCALE', true),
    ],
];