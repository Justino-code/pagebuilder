<?php

namespace Justino\PageBuilder\Blocks;

use Justino\PageBuilder\Contracts\Block;

class HeroBlock implements Block
{
    public static function type(): string
    {
        return 'hero';
    }
    
    public static function label(): string
    {
        return 'Hero Section';
    }
    
    public static function icon(): string
    {
        return '📱';
    }
    
    public static function schema(): array
    {
        return [
            'title' => [
                'type' => 'text',
                'label' => 'Title',
                'default' => 'Welcome to our website'
            ],
            'subtitle' => [
                'type' => 'text',
                'label' => 'Subtitle',
                'default' => 'This is a hero section'
            ],
            'background_image' => [
                'type' => 'media',
                'label' => 'Background Image',
                'default' => null
            ],
            'cta_text' => [
                'type' => 'text',
                'label' => 'Button Text',
                'default' => 'Get Started'
            ],
            'cta_link' => [
                'type' => 'text',
                'label' => 'Button Link',
                'default' => '#'
            ],
        ];
    }
    
    public static function defaults(): array
    {
        return [
            'title' => 'Welcome to our website',
            'subtitle' => 'This is a hero section',
            'background_image' => null,
            'cta_text' => 'Get Started',
            'cta_link' => '#',
        ];
    }
    
    public function render(array $data): string
    {
        return view('pagebuilder::blocks.hero', $data)->render();
    }
}