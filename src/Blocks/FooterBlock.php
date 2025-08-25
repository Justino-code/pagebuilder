<?php

namespace Justino\PageBuilder\Blocks;

use Justino\PageBuilder\Contracts\Block;

class FooterBlock implements Block
{
    public static function type(): string
    {
        return 'footer';
    }
    
    public static function label(): string
    {
        return 'Footer Template';
    }
    
    public static function icon(): string
    {
        return '🔻';
    }
    
    public static function schema(): array
    {
        return [
            'name' => [
                'type' => 'text',
                'label' => 'Template Name',
                'default' => 'Main Footer'
            ],
            'is_default' => [
                'type' => 'checkbox',
                'label' => 'Set as default',
                'default' => false
            ],
            'sections' => [
                'type' => 'repeater',
                'label' => 'Footer Sections',
                'fields' => [
                    'title' => ['type' => 'text', 'label' => 'Section Title', 'default' => 'Quick Links'],
                    'links' => [
                        'type' => 'repeater',
                        'label' => 'Links',
                        'fields' => [
                            'label' => ['type' => 'text', 'label' => 'Label', 'default' => 'Home'],
                            'url' => ['type' => 'text', 'label' => 'URL', 'default' => '/']
                        ],
                        'default' => [
                            ['label' => 'Home', 'url' => '/'],
                            ['label' => 'About', 'url' => '/about']
                        ]
                    ]
                ],
                'default' => [
                    [
                        'title' => 'Quick Links',
                        'links' => [
                            ['label' => 'Home', 'url' => '/'],
                            ['label' => 'About', 'url' => '/about']
                        ]
                    ]
                ]
            ],
            'copyright' => [
                'type' => 'text',
                'label' => 'Copyright Text',
                'default' => '© ' . date('Y') . ' My Website. All rights reserved.'
            ],
            'styles' => [
                'type' => 'style-group',
                'label' => 'Footer Styles',
                'fields' => [
                    'background_color' => ['type' => 'color', 'label' => 'Background', 'default' => '#f8f9fa'],
                    'text_color' => ['type' => 'color', 'label' => 'Text Color', 'default' => '#212529'],
                    'link_color' => ['type' => 'color', 'label' => 'Link Color', 'default' => '#007bff']
                ]
            ]
        ];
    }
    
    public static function defaults(): array
    {
        return [
            'name' => 'Main Footer',
            'is_default' => false,
            'sections' => [
                [
                    'title' => 'Quick Links',
                    'links' => [
                        ['label' => 'Home', 'url' => '/'],
                        ['label' => 'About', 'url' => '/about']
                    ]
                ]
            ],
            'copyright' => '© ' . date('Y') . ' My Website. All rights reserved.',
            'styles' => [
                'background_color' => '#f8f9fa',
                'text_color' => '#212529',
                'link_color' => '#007bff'
            ]
        ];
    }
    
    public function render(array $data): string
    {
        return view('pagebuilder::blocks.footer', $data)->render();
    }
}