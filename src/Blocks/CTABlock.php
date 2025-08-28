<?php

namespace Justino\PageBuilder\Blocks;

use Justino\PageBuilder\Contracts\Block;

class CTABlock extends BaseBlock implements Block
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
                'default' => 'Ready to get started?',
                'required' => true
            ],
            'description' => [
                'type' => 'textarea',
                'label' => 'Description',
                'default' => 'Join thousands of satisfied customers today.',
                'required' => false
            ],
            'button_text' => [
                'type' => 'text',
                'label' => 'Button Text',
                'default' => 'Get Started',
                'required' => true
            ],
            'button_link' => [
                'type' => 'text',
                'label' => 'Button Link',
                'default' => '#',
                'required' => true
            ],
            'background_color' => [
                'type' => 'color',
                'label' => 'Background Color',
                'default' => '#f3f4f6'
            ],
            'text_color' => [
                'type' => 'color',
                'label' => 'Text Color',
                'default' => '#000000'
            ],
            'button_color' => [
                'type' => 'color',
                'label' => 'Button Color',
                'default' => '#3b82f6'
            ],
            'button_text_color' => [
                'type' => 'color',
                'label' => 'Button Text Color',
                'default' => '#ffffff'
            ],
            'layout' => [
                'type' => 'select',
                'label' => 'Layout',
                'options' => [
                    'centered' => 'Centered',
                    'left' => 'Left Aligned',
                    'split' => 'Split Layout'
                ],
                'default' => 'centered'
            ]
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
            'text_color' => '#000000',
            'button_color' => '#3b82f6',
            'button_text_color' => '#ffffff',
            'layout' => 'centered'
        ];
    }
    
    public function render(array $data): string
    {
        $defaults = static::defaults();
        
        $title = $data['title'] ?? $defaults['title'];
        $description = $data['description'] ?? $defaults['description'];
        $buttonText = $data['button_text'] ?? $defaults['button_text'];
        $buttonLink = $data['button_link'] ?? $defaults['button_link'];
        $backgroundColor = $data['background_color'] ?? $defaults['background_color'];
        $textColor = $data['text_color'] ?? $defaults['text_color'];
        $buttonColor = $data['button_color'] ?? $defaults['button_color'];
        $buttonTextColor = $data['button_text_color'] ?? $defaults['button_text_color'];
        $layout = $data['layout'] ?? $defaults['layout'];
        
        $layoutClass = [
            'centered' => 'text-center',
            'left' => 'text-left',
            'split' => 'flex flex-col md:flex-row md:items-center md:justify-between'
        ][$layout] ?? 'text-center';
        
        $buttonStyle = "background-color: {$buttonColor}; color: {$buttonTextColor};";
        
        return "
            <section class='cta-section py-16 px-4' style='background-color: {$backgroundColor}; color: {$textColor};'>
                <div class='container mx-auto max-w-6xl'>
                    <div class='{$layoutClass}'>
                        " . ($layout !== 'split' ? "
                        <div class='mb-6'>
                            <h2 class='text-3xl font-bold mb-4'>{$title}</h2>
                            <p class='text-lg'>{$description}</p>
                        </div>
                        " : "
                        <div class='flex-1 mb-6 md:mb-0'>
                            <h2 class='text-3xl font-bold mb-2'>{$title}</h2>
                            <p class='text-lg'>{$description}</p>
                        </div>
                        ") . "
                        
                        <div class='" . ($layout === 'split' ? 'md:ml-8' : '') . "'>
                            <a href='{$buttonLink}' 
                               class='inline-block px-8 py-4 rounded-lg text-lg font-semibold hover:opacity-90 transition-opacity'
                               style='{$buttonStyle}'>
                                {$buttonText}
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        ";
    }
}