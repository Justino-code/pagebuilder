<?php

namespace Justino\PageBuilder\Blocks;

use Justino\PageBuilder\Contracts\Block;

class TextBlock extends BaseBlock implements Block
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
                'default' => '<p>Enter your text here</p>',
                'required' => true
            ],
            'text_align' => [
                'type' => 'select',
                'label' => 'Text Alignment',
                'options' => [
                    'left' => 'Left',
                    'center' => 'Center',
                    'right' => 'Right',
                    'justify' => 'Justify'
                ],
                'default' => 'left'
            ],
            'max_width' => [
                'type' => 'text',
                'label' => 'Max Width',
                'default' => 'none',
                'description' => 'e.g., 800px, 90%, none'
            ]
        ];
    }
    
    public static function defaults(): array
    {
        return [
            'content' => '<p>Enter your text here</p>',
            'text_align' => 'left',
            'max_width' => 'none'
        ];
    }
    
    public function render(array $data): string
    {
        $defaults = static::defaults();
        $content = $data['content'] ?? $defaults['content'];
        $textAlign = $data['text_align'] ?? $defaults['text_align'];
        $maxWidth = $data['max_width'] ?? $defaults['max_width'];
        
        $style = "text-align: {$textAlign};";
        if ($maxWidth !== 'none') {
            $style .= " max-width: {$maxWidth}; margin-left: auto; margin-right: auto;";
        }
        
        return "
            <div class='text-block' style='{$style}'>
                <div class='prose max-w-none'>
                    {$content}
                </div>
            </div>
        ";
    }
}