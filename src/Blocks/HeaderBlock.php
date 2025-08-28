<?php

namespace Justino\PageBuilder\Blocks;

use Justino\PageBuilder\Contracts\Block;

class HeaderBlock extends BaseBlock implements Block
{
    public static function type(): string
    {
        return 'header';
    }
    
    public static function label(): string
    {
        return 'Header Template';
    }
    
    public static function icon(): string
    {
        return '🔝';
    }
    
    public static function schema(): array
    {
        return [
            'name' => [
                'type' => 'text',
                'label' => 'Template Name',
                'default' => 'Main Header',
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
                    ],
                    'styles' => [
                        'type' => 'style-group',
                        'label' => 'Logo Styles',
                        'fields' => [
                            'color' => [
                                'type' => 'color',
                                'label' => 'Color',
                                'default' => '#000000'
                            ],
                            'font_size' => [
                                'type' => 'text',
                                'label' => 'Font Size',
                                'default' => '24px'
                            ],
                            'font_weight' => [
                                'type' => 'select',
                                'label' => 'Font Weight',
                                'options' => [
                                    'normal' => 'Normal',
                                    'bold' => 'Bold',
                                    'semibold' => 'Semibold'
                                ],
                                'default' => 'bold'
                            ]
                        ]
                    ]
                ]
            ],
            'menu_items' => [
                'type' => 'repeater',
                'label' => 'Menu Items',
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
                    ],
                    'is_button' => [
                        'type' => 'checkbox',
                        'label' => 'Style as Button',
                        'default' => false
                    ],
                    'styles' => [
                        'type' => 'style-group',
                        'label' => 'Item Styles',
                        'fields' => [
                            'color' => [
                                'type' => 'color',
                                'label' => 'Color',
                                'default' => '#333333'
                            ],
                            'hover_color' => [
                                'type' => 'color',
                                'label' => 'Hover Color',
                                'default' => '#007bff'
                            ],
                            'background_color' => [
                                'type' => 'color',
                                'label' => 'Background Color',
                                'default' => 'transparent'
                            ],
                            'hover_background' => [
                                'type' => 'color',
                                'label' => 'Hover Background',
                                'default' => 'transparent'
                            ]
                        ]
                    ]
                ],
                'default' => [
                    [
                        'label' => 'Home',
                        'url' => '/',
                        'target' => '_self',
                        'is_button' => false,
                        'styles' => [
                            'color' => '#333333',
                            'hover_color' => '#007bff',
                            'background_color' => 'transparent',
                            'hover_background' => 'transparent'
                        ]
                    ],
                    [
                        'label' => 'About',
                        'url' => '/about',
                        'target' => '_self',
                        'is_button' => false,
                        'styles' => [
                            'color' => '#333333',
                            'hover_color' => '#007bff',
                            'background_color' => 'transparent',
                            'hover_background' => 'transparent'
                        ]
                    ]
                ]
            ],
            'styles' => [
                'type' => 'style-group',
                'label' => 'Header Styles',
                'fields' => [
                    'background_color' => [
                        'type' => 'color',
                        'label' => 'Background',
                        'default' => '#ffffff'
                    ],
                    'text_color' => [
                        'type' => 'color',
                        'label' => 'Text Color',
                        'default' => '#000000'
                    ],
                    'padding' => [
                        'type' => 'text',
                        'label' => 'Padding',
                        'default' => '1rem 0'
                    ],
                    'sticky' => [
                        'type' => 'checkbox',
                        'label' => 'Sticky Header',
                        'default' => false
                    ],
                    'shadow' => [
                        'type' => 'select',
                        'label' => 'Shadow',
                        'options' => [
                            'none' => 'None',
                            'sm' => 'Small',
                            'md' => 'Medium',
                            'lg' => 'Large'
                        ],
                        'default' => 'none'
                    ]
                ]
            ]
        ];
    }
    
    public static function defaults(): array
    {
        return [
            'name' => 'Main Header',
            'is_default' => false,
            'logo' => [
                'type' => 'text',
                'text' => 'My Website',
                'image' => null,
                'link' => '/',
                'styles' => [
                    'color' => '#000000',
                    'font_size' => '24px',
                    'font_weight' => 'bold'
                ]
            ],
            'menu_items' => [
                [
                    'label' => 'Home',
                    'url' => '/',
                    'target' => '_self',
                    'is_button' => false,
                    'styles' => [
                        'color' => '#333333',
                        'hover_color' => '#007bff',
                        'background_color' => 'transparent',
                        'hover_background' => 'transparent'
                    ]
                ],
                [
                    'label' => 'About',
                    'url' => '/about',
                    'target' => '_self',
                    'is_button' => false,
                    'styles' => [
                        'color' => '#333333',
                        'hover_color' => '#007bff',
                        'background_color' => 'transparent',
                        'hover_background' => 'transparent'
                    ]
                ]
            ],
            'styles' => [
                'background_color' => '#ffffff',
                'text_color' => '#000000',
                'padding' => '1rem 0',
                'sticky' => false,
                'shadow' => 'none'
            ]
        ];
    }
    
    public function render(array $data): string
    {
        $defaults = static::defaults();
        
        $name = $data['name'] ?? $defaults['name'];
        $logo = $data['logo'] ?? $defaults['logo'];
        $menuItems = $data['menu_items'] ?? $defaults['menu_items'];
        $styles = $data['styles'] ?? $defaults['styles'];
        
        $headerId = 'header-' . uniqid();
        $stickyClass = $styles['sticky'] ? 'sticky top-0 z-50' : '';
        $shadowClass = $styles['shadow'] !== 'none' ? "shadow-{$styles['shadow']}" : '';
        
        $logoHtml = $this->renderLogo($logo);
        $menuHtml = $this->renderMenu($menuItems);
        
        return "
            <header id='{$headerId}' 
                    class='header-template {$stickyClass} {$shadowClass}'
                    style='background-color: {$styles['background_color']}; 
                           color: {$styles['text_color']};
                           padding: {$styles['padding']};'>
                
                <div class='container mx-auto px-4'>
                    <div class='flex justify-between items-center'>
                        <!-- Logo -->
                        <div class='logo'>
                            {$logoHtml}
                        </div>

                        <!-- Desktop Menu -->
                        <nav class='hidden md:block'>
                            <ul class='flex space-x-6 items-center'>
                                {$menuHtml}
                            </ul>
                        </nav>

                        <!-- Mobile Menu Button -->
                        <button class='mobile-menu-button md:hidden p-2' 
                                onclick='toggleMobileMenu(\"{$headerId}\")'>
                            <svg class='w-6 h-6' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                                <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' 
                                      d='M4 6h16M4 12h16M4 18h16'></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Mobile Menu -->
                    <div class='mobile-menu hidden md:hidden mt-4 pb-4'>
                        <ul class='space-y-2'>
                            {$menuHtml}
                        </ul>
                    </div>
                </div>
            </header>

            <style>
                #{$headerId} .menu-item {
                    transition: all 0.3s ease;
                }
                
                #{$headerId} .menu-item:hover {
                    color: var(--hover-color) !important;
                    background-color: var(--hover-bg) !important;
                }
                
                #{$headerId} .menu-item.button {
                    border-radius: 0.375rem;
                    padding: 0.5rem 1rem;
                }
            </style>

            <script>
                function toggleMobileMenu(headerId) {
                    const menu = document.querySelector(`#${headerId} .mobile-menu`);
                    menu.classList.toggle('hidden');
                }
            </script>
        ";
    }
    
    protected function renderLogo(array $logo): string
    {
        $logoType = $logo['type'] ?? 'text';
        $logoLink = $logo['link'] ?? '/';
        $logoStyles = $logo['styles'] ?? [];
        
        // Extrair valores com coalescência antes de usar na string
        $color = $logoStyles['color'] ?? '#000000';
        $fontSize = $logoStyles['font_size'] ?? '24px';
        $fontWeight = $logoStyles['font_weight'] ?? 'bold';
        
        $style = "color: {$color}; 
                 font-size: {$fontSize};
                 font-weight: {$fontWeight};";
        
        if ($logoType === 'text') {
            $logoText = $logo['text'] ?? 'My Website';
            return "<a href='{$logoLink}' class='logo-text font-bold' style='{$style}'>{$logoText}</a>";
        } else {
            $logoImage = $logo['image'] ?? '';
            $altText = $logo['text'] ?? 'Logo';
            return $logoImage ? 
                "<a href='{$logoLink}' class='logo-image'>
                    <img src='{$logoImage}' alt='{$altText}' style='max-height: 50px;'>
                 </a>" : 
                "<a href='{$logoLink}' class='logo-text' style='{$style}'>Logo</a>";
        }
    }

   protected function renderMenu(array $menuItems): string
    {
        $menuHtml = '';
        
        foreach ($menuItems as $item) {
            $label = $item['label'] ?? '';
            $url = $item['url'] ?? '#';
            $target = $item['target'] ?? '_self';
            $isButton = $item['is_button'] ?? false;
            $styles = $item['styles'] ?? [];
            
            // Extrair valores com coalescência antes de usar na string
            $hoverColor = $styles['hover_color'] ?? '#007bff';
            $hoverBg = $styles['hover_background'] ?? 'transparent';
            $color = $styles['color'] ?? '#333333';
            $backgroundColor = $styles['background_color'] ?? 'transparent';
            
            $itemClass = $isButton ? 'menu-item button' : 'menu-item';
            $style = "--hover-color: {$hoverColor};
                     --hover-bg: {$hoverBg};
                     color: {$color};
                     background-color: {$backgroundColor};";
            
            $menuHtml .= "
                <li>
                    <a href='{$url}' 
                       target='{$target}'
                       class='{$itemClass} px-3 py-2 rounded transition-colors'
                       style='{$style}'>
                        {$label}
                    </a>
                </li>
            ";
        }
        
        return $menuHtml;
    }
}