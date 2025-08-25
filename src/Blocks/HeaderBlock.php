<?php

namespace Justino\PageBuilder\Blocks;

use Justino\PageBuilder\Contracts\Block;

class HeaderBlock implements Block
{
    public static function type(): string
    {
        return 'header';
    }
    
    public static function label(): string
    {
        return 'Header Template';
    }
    
    public static function icon(): string
    {
        return '🔝';
    }
    
    public static function schema(): array
    {
        return [
            'name' => [
                'type' => 'text',
                'label' => 'Template Name',
                'default' => 'Main Header'
            ],
            'is_default' => [
                'type' => 'checkbox',
                'label' => 'Set as default',
                'default' => false
            ],
            'logo' => [
                'type' => 'group',
                'label' => 'Logo Settings',
                'fields' => [
                    'type' => [
                        'type' => 'select',
                        'label' => 'Logo Type',
                        'options' => [
                            'text' => 'Text',
                            'image' => 'Image'
                        ],
                        'default' => 'text'
                    ],
                    'text' => [
                        'type' => 'text',
                        'label' => 'Logo Text',
                        'default' => 'My Website',
                        'condition' => 'logo.type == "text"'
                    ],
                    'image' => [
                        'type' => 'media',
                        'label' => 'Logo Image',
                        'default' => null,
                        'condition' => 'logo.type == "image"'
                    ],
                    'styles' => [
                        'type' => 'style-group',
                        'label' => 'Logo Styles',
                        'fields' => [
                            'color' => ['type' => 'color', 'label' => 'Color', 'default' => '#000000'],
                            'font_size' => ['type' => 'text', 'label' => 'Font Size', 'default' => '24px']
                        ]
                    ]
                ]
            ],
            'menu_items' => [
                'type' => 'repeater',
                'label' => 'Menu Items',
                'fields' => [
                    'label' => ['type' => 'text', 'label' => 'Label', 'default' => 'Home'],
                    'url' => ['type' => 'text', 'label' => 'URL', 'default' => '/'],
                    'styles' => [
                        'type' => 'style-group',
                        'label' => 'Item Styles',
                        'fields' => [
                            'color' => ['type' => 'color', 'label' => 'Color', 'default' => '#333333'],
                            'hover_color' => ['type' => 'color', 'label' => 'Hover Color', 'default' => '#007bff']
                        ]
                    ]
                ],
                'default' => [
                    ['label' => 'Home', 'url' => '/', 'styles' => ['color' => '#333333', 'hover_color' => '#007bff']],
                    ['label' => 'About', 'url' => '/about', 'styles' => ['color' => '#333333', 'hover_color' => '#007bff']]
                ]
            ],
            'styles' => [
                'type' => 'style-group',
                'label' => 'Header Styles',
                'fields' => [
                    'background_color' => ['type' => 'color', 'label' => 'Background', 'default' => '#ffffff'],
                    'text_color' => ['type' => 'color', 'label' => 'Text Color', 'default' => '#000000'],
                    'padding' => ['type' => 'text', 'label' => 'Padding', 'default' => '1rem 0']
                ]
            ]
        ];
    }
    
    public static function defaults(): array
    {
        return [
            'name' => 'Main Header',
            'is_default' => false,
            'logo' => [
                'type' => 'text',
                'text' => 'My Website',
                'image' => null,
                'styles' => ['color' => '#000000', 'font_size' => '24px']
            ],
            'menu_items' => [
                [
                    'label' => 'Home',
                    'url' => '/',
                    'styles' => ['color' => '#333333', 'hover_color' => '#007bff']
                ],
                [
                    'label' => 'About',
                    'url' => '/about',
                    'styles' => ['color' => '#333333', 'hover_color' => '#007bff']
                ]
            ],
            'styles' => [
                'background_color' => '#ffffff',
                'text_color' => '#000000',
                'padding' => '1rem 0'
            ]
        ];
    }
    
    public function render(array $data): string
    {
        return view('pagebuilder::blocks.header', $data)->render();
    }
}