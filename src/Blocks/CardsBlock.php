<?php

namespace Justino\PageBuilder\Blocks;

use Justino\PageBuilder\Contracts\Block;

class CardsBlock extends BaseBlock implements Block
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
                'default' => 'Our Features',
                'required' => false
            ],
            'columns' => [
                'type' => 'select',
                'label' => 'Columns',
                'options' => [
                    '1' => '1 Column',
                    '2' => '2 Columns',
                    '3' => '3 Columns',
                    '4' => '4 Columns'
                ],
                'default' => '3'
            ],
            'cards' => [
                'type' => 'repeater',
                'label' => 'Cards',
                'fields' => [
                    'title' => [
                        'type' => 'text',
                        'label' => 'Card Title',
                        'default' => 'Feature',
                        'required' => true
                    ],
                    'description' => [
                        'type' => 'textarea',
                        'label' => 'Description',
                        'default' => 'Feature description',
                        'required' => false
                    ],
                    'icon' => [
                        'type' => 'text',
                        'label' => 'Icon',
                        'default' => '⭐',
                        'description' => 'Emoji or icon class'
                    ],
                    'image' => [
                        'type' => 'media',
                        'label' => 'Image',
                        'default' => null
                    ],
                    'link' => [
                        'type' => 'text',
                        'label' => 'Link',
                        'default' => '#'
                    ]
                ],
                'default' => [
                    [
                        'title' => 'Feature One',
                        'description' => 'First feature description',
                        'icon' => '⭐',
                        'image' => null,
                        'link' => '#'
                    ],
                    [
                        'title' => 'Feature Two',
                        'description' => 'Second feature description',
                        'icon' => '🚀',
                        'image' => null,
                        'link' => '#'
                    ]
                ]
            ]
        ];
    }
    
    public static function defaults(): array
    {
        return [
            'title' => 'Our Features',
            'columns' => '3',
            'cards' => [
                [
                    'title' => 'Feature One',
                    'description' => 'First feature description',
                    'icon' => '⭐',
                    'image' => null,
                    'link' => '#'
                ],
                [
                    'title' => 'Feature Two', 
                    'description' => 'Second feature description',
                    'icon' => '🚀',
                    'image' => null,
                    'link' => '#'
                ]
            ]
        ];
    }
    
    public function render(array $data): string
    {
        $defaults = static::defaults();
        
        $title = $data['title'] ?? $defaults['title'];
        $columns = $data['columns'] ?? $defaults['columns'];
        $cards = $data['cards'] ?? $defaults['cards'];
        
        $gridClass = [
            '1' => 'grid-cols-1',
            '2' => 'grid-cols-1 md:grid-cols-2',
            '3' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
            '4' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4'
        ][$columns] ?? 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3';
        
        $cardsHtml = '';
        foreach ($cards as $card) {
            $cardTitle = $card['title'] ?? '';
            $cardDescription = $card['description'] ?? '';
            $cardIcon = $card['icon'] ?? '';
            $cardImage = $card['image'] ?? null;
            $cardLink = $card['link'] ?? '#';
            
            $iconHtml = $cardImage ? 
                "<img src='{$cardImage}' alt='{$cardTitle}' class='w-16 h-16 mx-auto mb-4 rounded-full object-cover'>" :
                ($cardIcon ? "<div class='text-4xl mb-4'>{$cardIcon}</div>" : '');
            
            $cardsHtml .= "
                <div class='bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition-shadow'>
                    <a href='{$cardLink}' class='block'>
                        {$iconHtml}
                        <h3 class='text-xl font-bold mb-2'>{$cardTitle}</h3>
                        <p class='text-gray-600'>{$cardDescription}</p>
                    </a>
                </div>
            ";
        }
        
        return "
            <section class='cards-section py-16'>
                <div class='container mx-auto px-4'>
                    " . ($title ? "
                    <h2 class='text-3xl font-bold text-center mb-12'>{$title}</h2>
                    " : "") . "
                    
                    <div class='grid {$gridClass} gap-8'>
                        {$cardsHtml}
                    </div>
                </div>
            </section>
        ";
    }
}