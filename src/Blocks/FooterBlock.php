<?php

namespace Justino\PageBuilder\Blocks;

use Justino\PageBuilder\Contracts\Block;

class FooterBlock extends BaseBlock implements Block
{
    public static function type(): string
    {
        return 'footer';
    }
    
    public static function label(): string
    {
        return 'Footer Template';
    }
    
    public static function icon(): string
    {
        return '🔻';
    }
    
    public static function schema(): array
    {
        return [
            'name' => [
                'type' => 'text',
                'label' => 'Template Name',
                'default' => 'Main Footer',
                'required' => true
            ],
            'is_default' => [
                'type' => 'checkbox',
                'label' => 'Set as default',
                'default' => false
            ],
            'logo' => [
                'type' => 'group',
                'label' => 'Logo Settings',
                'fields' => [
                    'type' => [
                        'type' => 'select',
                        'label' => 'Logo Type',
                        'options' => [
                            'text' => 'Text',
                            'image' => 'Image'
                        ],
                        'default' => 'text'
                    ],
                    'text' => [
                        'type' => 'text',
                        'label' => 'Logo Text',
                        'default' => 'My Website',
                        'condition' => 'logo.type == "text"',
                        'required' => true
                    ],
                    'image' => [
                        'type' => 'media',
                        'label' => 'Logo Image',
                        'default' => null,
                        'condition' => 'logo.type == "image"',
                        'required' => true
                    ],
                    'link' => [
                        'type' => 'text',
                        'label' => 'Logo Link',
                        'default' => '/'
                    ]
                ]
            ],
            'sections' => [
                'type' => 'repeater',
                'label' => 'Footer Sections',
                'fields' => [
                    'title' => [
                        'type' => 'text',
                        'label' => 'Section Title',
                        'default' => 'Quick Links',
                        'required' => true
                    ],
                    'links' => [
                        'type' => 'repeater',
                        'label' => 'Links',
                        'fields' => [
                            'label' => [
                                'type' => 'text',
                                'label' => 'Label',
                                'default' => 'Home',
                                'required' => true
                            ],
                            'url' => [
                                'type' => 'text',
                                'label' => 'URL',
                                'default' => '/',
                                'required' => true
                            ],
                            'target' => [
                                'type' => 'select',
                                'label' => 'Target',
                                'options' => [
                                    '_self' => 'Same Tab',
                                    '_blank' => 'New Tab'
                                ],
                                'default' => '_self'
                            ]
                        ],
                        'default' => [
                            ['label' => 'Home', 'url' => '/', 'target' => '_self'],
                            ['label' => 'About', 'url' => '/about', 'target' => '_self']
                        ]
                    ]
                ],
                'default' => [
                    [
                        'title' => 'Quick Links',
                        'links' => [
                            ['label' => 'Home', 'url' => '/', 'target' => '_self'],
                            ['label' => 'About', 'url' => '/about', 'target' => '_self']
                        ]
                    ]
                ]
            ],
            'social_links' => [
                'type' => 'repeater',
                'label' => 'Social Links',
                'fields' => [
                    'platform' => [
                        'type' => 'select',
                        'label' => 'Platform',
                        'options' => [
                            'facebook' => 'Facebook',
                            'twitter' => 'Twitter',
                            'instagram' => 'Instagram',
                            'linkedin' => 'LinkedIn',
                            'youtube' => 'YouTube',
                            'tiktok' => 'TikTok',
                            'github' => 'GitHub',
                            'custom' => 'Custom'
                        ],
                        'default' => 'facebook'
                    ],
                    'url' => [
                        'type' => 'text',
                        'label' => 'URL',
                        'default' => '#',
                        'required' => true
                    ],
                    'icon' => [
                        'type' => 'text',
                        'label' => 'Custom Icon',
                        'default' => '',
                        'condition' => 'social_links.*.platform == "custom"'
                    ]
                ],
                'default' => []
            ],
            'copyright' => [
                'type' => 'text',
                'label' => 'Copyright Text',
                'default' => '© ' . date('Y') . ' My Website. All rights reserved.'
            ],
            'styles' => [
                'type' => 'style-group',
                'label' => 'Footer Styles',
                'fields' => [
                    'background_color' => [
                        'type' => 'color',
                        'label' => 'Background',
                        'default' => '#f8f9fa'
                    ],
                    'text_color' => [
                        'type' => 'color',
                        'label' => 'Text Color',
                        'default' => '#212529'
                    ],
                    'link_color' => [
                        'type' => 'color',
                        'label' => 'Link Color',
                        'default' => '#007bff'
                    ],
                    'hover_color' => [
                        'type' => 'color',
                        'label' => 'Hover Color',
                        'default' => '#0056b3'
                    ],
                    'border_color' => [
                        'type' => 'color',
                        'label' => 'Border Color',
                        'default' => '#dee2e6'
                    ]
                ]
            ]
        ];
    }
    
    public static function defaults(): array
    {
        return [
            'name' => 'Main Footer',
            'is_default' => false,
            'logo' => [
                'type' => 'text',
                'text' => 'My Website',
                'image' => null,
                'link' => '/'
            ],
            'sections' => [
                [
                    'title' => 'Quick Links',
                    'links' => [
                        ['label' => 'Home', 'url' => '/', 'target' => '_self'],
                        ['label' => 'About', 'url' => '/about', 'target' => '_self']
                    ]
                ]
            ],
            'social_links' => [],
            'copyright' => '© ' . date('Y') . ' My Website. All rights reserved.',
            'styles' => [
                'background_color' => '#f8f9fa',
                'text_color' => '#212529',
                'link_color' => '#007bff',
                'hover_color' => '#0056b3',
                'border_color' => '#dee2e6'
            ]
        ];
    }
    
    public function render(array $data): string
    {
        $defaults = static::defaults();
        
        $name = $data['name'] ?? $defaults['name'];
        $logo = $data['logo'] ?? $defaults['logo'];
        $sections = $data['sections'] ?? $defaults['sections'];
        $socialLinks = $data['social_links'] ?? $defaults['social_links'];
        $copyright = $data['copyright'] ?? $defaults['copyright'];
        $styles = $data['styles'] ?? $defaults['styles'];
        
        $footerId = 'footer-' . uniqid();
        
        $logoHtml = $this->renderLogo($logo);
        $sectionsHtml = $this->renderSections($sections, $styles);
        $socialHtml = $this->renderSocialLinks($socialLinks);
        
        return "
            <footer id='{$footerId}' class='footer-template' 
                    style='background-color: {$styles['background_color']}; 
                           color: {$styles['text_color']};'>
                
                <div class='container mx-auto px-4 py-12'>
                    <div class='grid grid-cols-1 md:grid-cols-".(count($sections) + 1)." gap-8 mb-8'>
                        <!-- Logo Column -->
                        <div class='footer-logo-column'>
                            {$logoHtml}
                            " . (!empty($socialLinks) ? "
                            <div class='mt-6'>
                                <h4 class='font-semibold mb-4'>Follow Us</h4>
                                <div class='flex space-x-4'>
                                    {$socialHtml}
                                </div>
                            </div>
                            " : "") . "
                        </div>
                        
                        <!-- Sections -->
                        {$sectionsHtml}
                    </div>
                    
                    <!-- Copyright -->
                    <div class='border-t pt-6 text-center' 
                         style='border-color: {$styles['border_color']};'>
                        <p class='copyright-text text-sm'>{$copyright}</p>
                    </div>
                </div>
            </footer>

            <style>
                #{$footerId} a {
                    color: {$styles['link_color']};
                    transition: color 0.3s ease;
                }
                
                #{$footerId} a:hover {
                    color: {$styles['hover_color']};
                }
                
                #{$footerId} .social-link {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    width: 40px;
                    height: 40px;
                    border-radius: 50%;
                    background-color: rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease;
                }
                
                #{$footerId} .social-link:hover {
                    background-color: {$styles['link_color']};
                    color: white;
                    transform: translateY(-2px);
                }
            </style>
        ";
    }
    
    protected function renderLogo(array $logo): string
    {
        $logoType = $logo['type'] ?? 'text';
        $logoLink = $logo['link'] ?? '/';
        
        if ($logoType === 'text') {
            $logoText = $logo['text'] ?? 'My Website';
            return "
                <div class='footer-logo mb-4'>
                    <a href='{$logoLink}' class='text-2xl font-bold'>
                        {$logoText}
                    </a>
                </div>
            ";
        } else {
            $logoImage = $logo['image'] ?? '';
            $altText = $logo['text'] ?? 'Logo';
            return $logoImage ? "
                <div class='footer-logo mb-4'>
                    <a href='{$logoLink}'>
                        <img src='{$logoImage}' alt='{$altText}' class='h-12'>
                    </a>
                </div>
            " : "
                <div class='footer-logo mb-4'>
                    <a href='{$logoLink}' class='text-2xl font-bold'>Logo</a>
                </div>
            ";
        }
    }
    
    protected function renderSections(array $sections, array $styles): string
    {
        $sectionsHtml = '';
        
        foreach ($sections as $section) {
            $title = $section['title'] ?? '';
            $links = $section['links'] ?? [];
            
            $linksHtml = '';
            foreach ($links as $link) {
                $label = $link['label'] ?? '';
                $url = $link['url'] ?? '#';
                $target = $link['target'] ?? '_self';
                
                $linksHtml .= "
                    <li class='mb-2'>
                        <a href='{$url}' target='{$target}' class='hover:underline'>
                            {$label}
                        </a>
                    </li>
                ";
            }
            
            $sectionsHtml .= "
                <div class='footer-section'>
                    <h4 class='font-semibold mb-4'>{$title}</h4>
                    <ul class='space-y-2'>
                        {$linksHtml}
                    </ul>
                </div>
            ";
        }
        
        return $sectionsHtml;
    }
    
    protected function renderSocialLinks(array $socialLinks): string
    {
        $socialHtml = '';
        $icons = [
            'facebook' => '📘',
            'twitter' => '🐦',
            'instagram' => '📸',
            'linkedin' => '💼',
            'youtube' => '📺',
            'tiktok' => '🎵',
            'github' => '🐙'
        ];
        
        foreach ($socialLinks as $link) {
            $platform = $link['platform'] ?? '';
            $url = $link['url'] ?? '#';
            $customIcon = $link['icon'] ?? '';
            $icon = $customIcon ?: ($icons[$platform] ?? '🔗');
            
            $socialHtml .= "
                <a href='{$url}' 
                   target='_blank'
                   class='social-link'
                   title='".ucfirst($platform)."'>
                    {$icon}
                </a>
            ";
        }
        
        return $socialHtml;
    }
}