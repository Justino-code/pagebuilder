<?php

namespace Justino\PageBuilder\Blocks;

use Justino\PageBuilder\Contracts\Block;

class GalleryBlock implements Block
{
    public static function type(): string
    {
        return 'gallery';
    }
    
    public static function label(): string
    {
        return 'Image Gallery';
    }
    
    public static function icon(): string
    {
        return '🖼️';
    }
    
    public static function schema(): array
    {
        return [
            'title' => [
                'type' => 'text',
                'label' => 'Gallery Title',
                'default' => 'Our Gallery'
            ],
            'images' => [
                'type' => 'repeater',
                'label' => 'Images',
                'fields' => [
                    'image' => [
                        'type' => 'media',
                        'label' => 'Image',
                        'default' => null
                    ],
                    'caption' => [
                        'type' => 'text',
                        'label' => 'Caption',
                        'default' => ''
                    ],
                ],
                'default' => []
            ],
            'columns' => [
                'type' => 'select',
                'label' => 'Columns',
                'options' => [
                    '2' => '2 Columns',
                    '3' => '3 Columns',
                    '4' => '4 Columns',
                ],
                'default' => '3'
            ],
        ];
    }
    
    public static function defaults(): array
    {
        return [
            'title' => 'Our Gallery',
            'images' => [],
            'columns' => '3',
        ];
    }
    
    public function render(array $data): string
    {
        return view('pagebuilder::blocks.gallery', $data)->render();
    }
}