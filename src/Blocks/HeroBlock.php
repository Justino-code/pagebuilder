<?php

namespace Justino\PageBuilder\Blocks;

use Justino\PageBuilder\Contracts\Block;

class HeroBlock extends BaseBlock implements Block
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
                'default' => 'Welcome to our website',
                'required' => true
            ],
            'subtitle' => [
                'type' => 'text',
                'label' => 'Subtitle',
                'default' => 'This is a hero section',
                'required' => false
            ],
            'background_image' => [
                'type' => 'media',
                'label' => 'Background Image',
                'default' => null,
                'description' => 'Upload or select a background image'
            ],
            'cta_text' => [
                'type' => 'text',
                'label' => 'Button Text',
                'default' => 'Get Started',
                'required' => false
            ],
            'cta_link' => [
                'type' => 'text',
                'label' => 'Button Link',
                'default' => '#',
                'required' => false
            ],
            'text_color' => [
                'type' => 'color',
                'label' => 'Text Color',
                'default' => '#ffffff'
            ],
            'overlay_color' => [
                'type' => 'color',
                'label' => 'Overlay Color',
                'default' => 'rgba(0, 0, 0, 0.4)'
            ],
            'content_align' => [
                'type' => 'select',
                'label' => 'Content Alignment',
                'options' => [
                    'left' => 'Left',
                    'center' => 'Center',
                    'right' => 'Right'
                ],
                'default' => 'center'
            ],
            'min_height' => [
                'type' => 'text',
                'label' => 'Minimum Height',
                'default' => '500px',
                'description' => 'e.g., 500px, 80vh'
            ]
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
            'text_color' => '#ffffff',
            'overlay_color' => 'rgba(0, 0, 0, 0.4)',
            'content_align' => 'center',
            'min_height' => '500px'
        ];
    }
    
    public function render(array $data): string
    {
        $defaults = static::defaults();
        
        $title = $data['title'] ?? $defaults['title'];
        $subtitle = $data['subtitle'] ?? $defaults['subtitle'];
        $backgroundImage = $data['background_image'] ?? $defaults['background_image'];
        $ctaText = $data['cta_text'] ?? $defaults['cta_text'];
        $ctaLink = $data['cta_link'] ?? $defaults['cta_link'];
        $textColor = $data['text_color'] ?? $defaults['text_color'];
        $overlayColor = $data['overlay_color'] ?? $defaults['overlay_color'];
        $contentAlign = $data['content_align'] ?? $defaults['content_align'];
        $minHeight = $data['min_height'] ?? $defaults['min_height'];
        
        $backgroundStyle = $backgroundImage ? 
            "background-image: url('{$backgroundImage}');" : 
            "background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);";
        
        $textAlignClass = [
            'left' => 'text-left',
            'center' => 'text-center',
            'right' => 'text-right'
        ][$contentAlign] ?? 'text-center';
        
        return "
            <section class='hero-section bg-cover bg-center bg-no-repeat relative' 
                     style='{$backgroundStyle}; min-height: {$minHeight};'>
                <div class='absolute inset-0' style='background: {$overlayColor};'></div>
                <div class='container mx-auto px-4 relative z-10 h-full flex items-center'>
                    <div class='w-full {$textAlignClass}' style='color: {$textColor};'>
                        <h1 class='text-4xl md:text-5xl font-bold mb-4'>{$title}</h1>
                        <p class='text-xl md:text-2xl mb-8'>{$subtitle}</p>
                        " . ($ctaText ? "
                        <a href='{$ctaLink}' 
                           class='inline-block bg-blue-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-blue-700 transition-colors'>
                            {$ctaText}
                        </a>
                        " : "") . "
                    </div>
                </div>
            </section>
        ";
    }
}