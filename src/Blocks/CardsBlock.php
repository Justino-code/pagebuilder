<?php

namespace Justino\PageBuilder\Blocks;

use Justino\PageBuilder\Contracts\Block;

class CardsBlock implements Block
{
    public static function type(): string
    {
        return 'cards';
    }
    
    public static function label(): string
    {
        return 'Cards Grid';
    }
    
    public static function icon(): string
    {
        return '🃏';
    }
    
    public static function schema(): array
    {
        return [
            'title' => [
                'type' => 'text',
                'label' => 'Section Title',
                'default' => 'Our Features'
            ],
            'cards' => [
                'type' => 'repeater',
                'label' => 'Cards',
                'fields' => [
                    'title' => [
                        'type' => 'text',
                        'label' => 'Card Title',
                        'default' => 'Feature'
                    ],
                    'description' => [
                        'type' => 'text',
                        'label' => 'Description',
                        'default' => 'Feature description'
                    ],
                    'icon' => [
                        'type' => 'text',
                        'label' => 'Icon',
                        'default' => '⭐'
                    ],
                    'image' => [
                        'type' => 'media',
                        'label' => 'Image',
                        'default' => null
                    ],
                ],
                'default' => [
                    [
                        'title' => 'Feature One',
                        'description' => 'First feature description',
                        'icon' => '⭐',
                        'image' => null
                    ],
                    [
                        'title' => 'Feature Two',
                        'description' => 'Second feature description',
                        'icon' => '🚀',
                        'image' => null
                    ]
                ]
            ],
        ];
    }
    
    public static function defaults(): array
    {
        return [
            'title' => 'Our Features',
            'cards' => [
                [
                    'title' => 'Feature One',
                    'description' => 'First feature description',
                    'icon' => '⭐',
                    'image' => null
                ],
                [
                    'title' => 'Feature Two', 
                    'description' => 'Second feature description',
                    'icon' => '🚀',
                    'image' => null
                ]
            ],
        ];
    }
    
    public function render(array $data): string
    {
        return view('pagebuilder::blocks.cards', $data)->render();
    }
}