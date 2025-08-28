<?php

namespace Justino\PageBuilder\Blocks;

use Justino\PageBuilder\Contracts\Block;

class GalleryBlock extends BaseBlock implements Block
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
                'default' => 'Our Gallery',
                'required' => false
            ],
            'description' => [
                'type' => 'textarea',
                'label' => 'Description',
                'default' => '',
                'required' => false
            ],
            'columns' => [
                'type' => 'select',
                'label' => 'Columns',
                'options' => [
                    '2' => '2 Columns',
                    '3' => '3 Columns',
                    '4' => '4 Columns',
                    '5' => '5 Columns',
                    '6' => '6 Columns'
                ],
                'default' => '3'
            ],
            'image_aspect_ratio' => [
                'type' => 'select',
                'label' => 'Image Aspect Ratio',
                'options' => [
                    '1/1' => 'Square (1:1)',
                    '4/3' => 'Standard (4:3)',
                    '16/9' => 'Widescreen (16:9)',
                    '3/2' => 'Classic (3:2)',
                    'free' => 'Free (Original)'
                ],
                'default' => '1/1'
            ],
            'show_captions' => [
                'type' => 'checkbox',
                'label' => 'Show Captions',
                'default' => true
            ],
            'lightbox_enabled' => [
                'type' => 'checkbox',
                'label' => 'Enable Lightbox',
                'default' => true
            ],
            'images' => [
                'type' => 'repeater',
                'label' => 'Images',
                'fields' => [
                    'image' => [
                        'type' => 'media',
                        'label' => 'Image',
                        'default' => null,
                        'required' => true
                    ],
                    'caption' => [
                        'type' => 'text',
                        'label' => 'Caption',
                        'default' => '',
                        'required' => false
                    ],
                    'alt_text' => [
                        'type' => 'text',
                        'label' => 'Alt Text',
                        'default' => '',
                        'required' => false
                    ],
                    'link' => [
                        'type' => 'text',
                        'label' => 'Link',
                        'default' => '',
                        'required' => false
                    ]
                ],
                'default' => []
            ],
            'styles' => [
                'type' => 'style-group',
                'label' => 'Gallery Styles',
                'fields' => [
                    'gap' => [
                        'type' => 'text',
                        'label' => 'Gap Between Images',
                        'default' => '0.5rem'
                    ],
                    'border_radius' => [
                        'type' => 'text',
                        'label' => 'Border Radius',
                        'default' => '0.5rem'
                    ],
                    'hover_effect' => [
                        'type' => 'select',
                        'label' => 'Hover Effect',
                        'options' => [
                            'none' => 'None',
                            'zoom' => 'Zoom',
                            'grayscale' => 'Grayscale',
                            'shadow' => 'Shadow'
                        ],
                        'default' => 'zoom'
                    ]
                ]
            ]
        ];
    }
    
    public static function defaults(): array
    {
        return [
            'title' => 'Our Gallery',
            'description' => '',
            'columns' => '3',
            'image_aspect_ratio' => '1/1',
            'show_captions' => true,
            'lightbox_enabled' => true,
            'images' => [],
            'styles' => [
                'gap' => '0.5rem',
                'border_radius' => '0.5rem',
                'hover_effect' => 'zoom'
            ]
        ];
    }
    
    public function render(array $data): string
    {
        $defaults = static::defaults();
        
        $title = $data['title'] ?? $defaults['title'];
        $description = $data['description'] ?? $defaults['description'];
        $columns = $data['columns'] ?? $defaults['columns'];
        $aspectRatio = $data['image_aspect_ratio'] ?? $defaults['image_aspect_ratio'];
        $showCaptions = $data['show_captions'] ?? $defaults['show_captions'];
        $lightboxEnabled = $data['lightbox_enabled'] ?? $defaults['lightbox_enabled'];
        $images = $data['images'] ?? $defaults['images'];
        $styles = $data['styles'] ?? $defaults['styles'];
        
        $gridClass = "grid grid-cols-2 md:grid-cols-{$columns}";
        $gapStyle = "gap: {$styles['gap']};";
        $galleryId = 'gallery-' . uniqid();
        
        $aspectRatioClass = $aspectRatio === 'free' ? '' : "aspect-ratio-{$aspectRatio}";
        $aspectRatioStyle = $aspectRatio === 'free' ? '' : "aspect-ratio: {$aspectRatio};";
        
        $hoverClass = match($styles['hover_effect'] ?? 'zoom') {
            'zoom' => 'hover:scale-105',
            'grayscale' => 'hover:grayscale-0 grayscale',
            'shadow' => 'hover:shadow-xl',
            default => ''
        };
        
        $imagesHtml = '';
        foreach ($images as $index => $image) {
            $imageUrl = $image['image'] ?? '';
            $caption = $image['caption'] ?? '';
            $altText = $image['alt_text'] ?? $caption;
            $link = $image['link'] ?? '';
            
            if (empty($imageUrl)) continue;
            
            $imageElement = "
                <img src='{$imageUrl}' alt='{$altText}' 
                     class='w-full h-full object-cover rounded-{$styles['border_radius']} transition-all duration-300 {$hoverClass}'>
            ";
            
            $content = $lightboxEnabled ? "
                <a href='{$imageUrl}' 
                   class='gallery-item' 
                   data-caption='{$caption}'
                   data-fancybox='{$galleryId}'>
                   {$imageElement}
                </a>
            " : ($link ? "
                <a href='{$link}' class='gallery-item'>
                    {$imageElement}
                </a>
            " : $imageElement);
            
            $imagesHtml .= "
                <div class='gallery-item-container' style='{$aspectRatioStyle}'>
                    <div class='relative overflow-hidden rounded-{$styles['border_radius']}'>
                        {$content}
                        " . ($showCaptions && $caption ? "
                        <div class='absolute bottom-0 left-0 right-0 bg-black bg-opacity-60 text-white p-2'>
                            <p class='text-sm'>{$caption}</p>
                        </div>
                        " : "") . "
                    </div>
                </div>
            ";
        }
        
        $lightboxScript = $lightboxEnabled ? "
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    if (typeof Fancybox !== 'undefined') {
                        Fancybox.bind('[data-fancybox=\"{$galleryId}\"]', {
                            Thumbs: false,
                            Toolbar: false,
                            infinite: false
                        });
                    }
                });
            </script>
        " : '';
        
        return "
            <section class='gallery-section py-16'>
                <div class='container mx-auto px-4'>
                    " . ($title ? "<h2 class='text-3xl font-bold text-center mb-4'>{$title}</h2>" : "") . "
                    " . ($description ? "<p class='text-center mb-12 text-gray-600'>{$description}</p>" : "") . "
                    
                    <div class='{$gridClass}' style='{$gapStyle}'>
                        {$imagesHtml}
                    </div>
                </div>
            </section>
            {$lightboxScript}
            
            <style>
                .gallery-item-container {
                    position: relative;
                    {$aspectRatioStyle}
                }
                .aspect-ratio-1\\/1 { aspect-ratio: 1/1; }
                .aspect-ratio-4\\/3 { aspect-ratio: 4/3; }
                .aspect-ratio-16\\/9 { aspect-ratio: 16/9; }
                .aspect-ratio-3\\/2 { aspect-ratio: 3/2; }
            </style>
        ";
    }
}