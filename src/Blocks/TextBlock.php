<?php

namespace Justino\PageBuilder\Blocks;

use Justino\PageBuilder\Contracts\Block;

class TextBlock implements Block
{
    public static function type(): string
    {
        return 'text';
    }
    
    public static function label(): string
    {
        return 'Text Content';
    }
    
    public static function icon(): string
    {
        return '📝';
    }
    
    public static function schema(): array
    {
        return [
            'content' => [
                'type' => 'richtext',
                'label' => 'Content',
                'default' => '<p>Enter your text here</p>'
            ],
        ];
    }
    
    public static function defaults(): array
    {
        return [
            'content' => '<p>Enter your text here</p>',
        ];
    }
    
    public function render(array $data): string
    {
        return view('pagebuilder::blocks.text', $data)->render();
    }
}