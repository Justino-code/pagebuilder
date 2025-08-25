<?php

namespace Justino\PageBuilder\Blocks;

use Justino\PageBuilder\Contracts\Block;

class CTABlock implements Block
{
    public static function type(): string
    {
        return 'cta';
    }
    
    public static function label(): string
    {
        return 'Call to Action';
    }
    
    public static function icon(): string
    {
        return '📢';
    }
    
    public static function schema(): array
    {
        return [
            'title' => [
                'type' => 'text',
                'label' => 'Title',
                'default' => 'Ready to get started?'
            ],
            'description' => [
                'type' => 'text',
                'label' => 'Description',
                'default' => 'Join thousands of satisfied customers today.'
            ],
            'button_text' => [
                'type' => 'text',
                'label' => 'Button Text',
                'default' => 'Get Started'
            ],
            'button_link' => [
                'type' => 'text',
                'label' => 'Button Link',
                'default' => '#'
            ],
            'background_color' => [
                'type' => 'color',
                'label' => 'Background Color',
                'default' => '#f3f4f6'
            ],
        ];
    }
    
    public static function defaults(): array
    {
        return [
            'title' => 'Ready to get started?',
            'description' => 'Join thousands of satisfied customers today.',
            'button_text' => 'Get Started',
            'button_link' => '#',
            'background_color' => '#f3f4f6',
        ];
    }
    
    public function render(array $data): string
    {
        return view('pagebuilder::blocks.cta', $data)->render();
    }
}