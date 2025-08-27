<?php

namespace Justino\PageBuilder\Http\Livewire;

use Livewire\Component;
use Justino\PageBuilder\Helpers\Translator;

class AdvancedStyleEditor extends Component
{
    public $styles = [
        'global' => [
            'font_family' => 'inter',
            'primary_color' => '#3b82f6',
            'secondary_color' => '#64748b',
            'background_color' => '#ffffff',
            'text_color' => '#1f2937',
        ],
        'typography' => [
            'heading_size' => '2xl',
            'body_size' => 'base',
            'line_height' => 'normal',
            'letter_spacing' => 'normal',
            'font_weight' => 'normal',
        ],
        'spacing' => [
            'container_padding' => '6',
            'section_spacing' => '8',
            'element_spacing' => '4',
            'button_padding' => '3',
            'input_padding' => '3',
        ],
        'borders' => [
            'border_width' => '1',
            'border_radius' => 'md',
            'border_color' => '#e5e7eb',
            'border_style' => 'solid',
        ],
        'effects' => [
            'shadow_intensity' => 'md',
            'hover_effect' => 'lift',
            'transition_speed' => '300',
            'opacity' => '100',
        ],
        'custom' => []
    ];

    public $currentTab = 'global';
    public $customClassName = '';
    public $customStyles = [];
    public $selectedElement = null;
    public $customCss = '';

    public $fontOptions = [
        'inter' => 'Inter', 'roboto' => 'Roboto', 'open-sans' => 'Open Sans',
        'poppins' => 'Poppins', 'montserrat' => 'Montserrat', 'system' => 'System',
        'lora' => 'Lora', 'playfair' => 'Playfair Display', 'source-sans' => 'Source Sans Pro'
    ];

    public $sizeOptions = [
        'xs' => 'Extra Small', 'sm' => 'Small', 'base' => 'Base', 
        'lg' => 'Large', 'xl' => 'Extra Large', '2xl' => '2X Large',
        '3xl' => '3X Large', '4xl' => '4X Large'
    ];

    public $spacingOptions = [
        '0' => 'None', '1' => 'XS', '2' => 'Small', '3' => 'Medium',
        '4' => 'Large', '6' => 'XL', '8' => '2XL', '12' => '3XL',
        '16' => '4XL', '20' => '5XL', '24' => '6XL'
    ];

    public $radiusOptions = [
        'none' => 'None', 'sm' => 'Small', 'md' => 'Medium',
        'lg' => 'Large', 'xl' => 'XL', 'full' => 'Full'
    ];

    public $shadowOptions = [
        'none' => 'None', 'sm' => 'Small', 'md' => 'Medium',
        'lg' => 'Large', 'xl' => 'XL', '2xl' => '2XL'
    ];

    public $hoverEffects = [
        'none' => 'None', 'lift' => 'Lift', 'scale' => 'Scale',
        'glow' => 'Glow', 'border' => 'Border Highlight', 'fade' => 'Fade'
    ];

    public $fontWeightOptions = [
        'light' => 'Light (300)', 'normal' => 'Normal (400)', 'medium' => 'Medium (500)',
        'semibold' => 'Semi Bold (600)', 'bold' => 'Bold (700)', 'extrabold' => 'Extra Bold (800)'
    ];

    public $borderStyleOptions = [
        'solid' => 'Solid', 'dashed' => 'Dashed', 'dotted' => 'Dotted',
        'double' => 'Double', 'groove' => 'Groove', 'ridge' => 'Ridge'
    ];

    public $opacityOptions = [
        '0' => '0%', '25' => '25%', '50' => '50%', 
        '75' => '75%', '90' => '90%', '100' => '100%'
    ];

    protected $listeners = ['elementSelected' => 'selectElement'];

    public function mount($initialStyles = [])
    {
        $this->styles = array_merge_recursive($this->styles, $initialStyles);
        
        // Inicializar custom CSS se existir
        if (!empty($this->styles['custom_css'])) {
            $this->customCss = $this->styles['custom_css'];
        }
    }

    public function selectElement($elementType, $elementId = null)
    {
        $this->selectedElement = [
            'type' => $elementType,
            'id' => $elementId,
            'class' => 'element-' . ($elementId ?: $elementType)
        ];
        $this->currentTab = 'custom';
    }

    public function addCustomClass()
    {
        if ($this->customClassName && $this->selectedElement) {
            $classKey = $this->customClassName;
            $this->styles['custom'][$classKey] = $this->customStyles;
            $this->customClassName = '';
            $this->customStyles = [];
        }
    }

    public function removeCustomClass($className)
    {
        if (isset($this->styles['custom'][$className])) {
            unset($this->styles['custom'][$className]);
        }
    }

    public function updateCustomStyle($className, $property, $value)
    {
        if (isset($this->styles['custom'][$className])) {
            $this->styles['custom'][$className][$property] = $value;
        }
    }

    public function resetStyles($category = null)
    {
        if ($category && isset($this->styles[$category])) {
            // Restaurar valores padrão para a categoria específica
            $defaults = [
                'global' => [
                    'font_family' => 'inter',
                    'primary_color' => '#3b82f6',
                    'secondary_color' => '#64748b',
                    'background_color' => '#ffffff',
                    'text_color' => '#1f2937',
                ],
                'typography' => [
                    'heading_size' => '2xl',
                    'body_size' => 'base',
                    'line_height' => 'normal',
                    'letter_spacing' => 'normal',
                    'font_weight' => 'normal',
                ],
                'spacing' => [
                    'container_padding' => '6',
                    'section_spacing' => '8',
                    'element_spacing' => '4',
                    'button_padding' => '3',
                    'input_padding' => '3',
                ],
                'borders' => [
                    'border_width' => '1',
                    'border_radius' => 'md',
                    'border_color' => '#e5e7eb',
                    'border_style' => 'solid',
                ],
                'effects' => [
                    'shadow_intensity' => 'md',
                    'hover_effect' => 'lift',
                    'transition_speed' => '300',
                    'opacity' => '100',
                ]
            ];

            if (isset($defaults[$category])) {
                $this->styles[$category] = $defaults[$category];
            }
        } else {
            // Reset completo
            $this->styles = [
                'global' => [
                    'font_family' => 'inter',
                    'primary_color' => '#3b82f6',
                    'secondary_color' => '#64748b',
                    'background_color' => '#ffffff',
                    'text_color' => '#1f2937',
                ],
                'typography' => [
                    'heading_size' => '2xl',
                    'body_size' => 'base',
                    'line_height' => 'normal',
                    'letter_spacing' => 'normal',
                    'font_weight' => 'normal',
                ],
                'spacing' => [
                    'container_padding' => '6',
                    'section_spacing' => '8',
                    'element_spacing' => '4',
                    'button_padding' => '3',
                    'input_padding' => '3',
                ],
                'borders' => [
                    'border_width' => '1',
                    'border_radius' => 'md',
                    'border_color' => '#e5e7eb',
                    'border_style' => 'solid',
                ],
                'effects' => [
                    'shadow_intensity' => 'md',
                    'hover_effect' => 'lift',
                    'transition_speed' => '300',
                    'opacity' => '100',
                ],
                'custom' => []
            ];
            $this->customCss = '';
        }
    }

    public function generateCss()
    {
        $css = [];
        
        // Global Styles
        $css[] = $this->generateGlobalCss();
        
        // Typography
        $css[] = $this->generateTypographyCss();
        
        // Spacing
        $css[] = $this->generateSpacingCss();
        
        // Borders
        $css[] = $this->generateBordersCss();
        
        // Effects
        $css[] = $this->generateEffectsCss();
        
        // Custom Styles
        $css[] = $this->generateCustomCss();
        
        // Custom CSS
        if (!empty($this->customCss)) {
            $css[] = $this->customCss;
        }

        return implode("\n", array_filter($css));
    }

    protected function generateGlobalCss()
    {
        $g = $this->styles['global'];
        $fonts = [
            'inter' => 'Inter, system-ui, sans-serif',
            'roboto' => 'Roboto, system-ui, sans-serif',
            'open-sans' => '"Open Sans", system-ui, sans-serif',
            'poppins' => 'Poppins, system-ui, sans-serif',
            'montserrat' => 'Montserrat, system-ui, sans-serif',
            'system' => 'system-ui, -apple-system, sans-serif',
            'lora' => 'Lora, serif',
            'playfair' => '"Playfair Display", serif',
            'source-sans' => '"Source Sans Pro", sans-serif'
        ];

        return <<<CSS
        :root {
            --font-family: {$fonts[$g['font_family']]};
            --primary-color: {$g['primary_color']};
            --secondary-color: {$g['secondary_color']};
            --background-color: {$g['background_color']};
            --text-color: {$g['text_color']};
        }
        
        body {
            font-family: var(--font-family);
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.6;
        }
        
        .text-primary { color: var(--primary-color); }
        .bg-primary { background-color: var(--primary-color); }
        .border-primary { border-color: var(--primary-color); }
        
        .text-secondary { color: var(--secondary-color); }
        .bg-secondary { background-color: var(--secondary-color); }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: color-mix(in srgb, var(--primary-color), black 15%);
            border-color: color-mix(in srgb, var(--primary-color), black 15%);
        }
        CSS;
    }

    protected function generateTypographyCss()
    {
        $t = $this->styles['typography'];
        $sizes = [
            'xs' => '0.75rem', 'sm' => '0.875rem', 'base' => '1rem',
            'lg' => '1.125rem', 'xl' => '1.25rem', '2xl' => '1.5rem',
            '3xl' => '1.875rem', '4xl' => '2.25rem'
        ];

        $weights = [
            'light' => '300', 'normal' => '400', 'medium' => '500',
            'semibold' => '600', 'bold' => '700', 'extrabold' => '800'
        ];

        return <<<CSS
        :root {
            --heading-font-size: {$sizes[$t['heading_size']]};
            --body-font-size: {$sizes[$t['body_size']]};
            --font-weight: {$weights[$t['font_weight']]};
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-weight: var(--font-weight);
            line-height: 1.3;
        }
        
        h1 { font-size: calc(var(--heading-font-size) * 1.5); }
        h2 { font-size: calc(var(--heading-font-size) * 1.3); }
        h3 { font-size: var(--heading-font-size); }
        
        body, p {
            font-size: var(--body-font-size);
            line-height: {$t['line_height']};
            letter-spacing: {$t['letter_spacing']};
        }
        
        .font-light { font-weight: 300; }
        .font-normal { font-weight: 400; }
        .font-medium { font-weight: 500; }
        .font-semibold { font-weight: 600; }
        .font-bold { font-weight: 700; }
        CSS;
    }

    protected function generateSpacingCss()
    {
        $s = $this->styles['spacing'];
        $spacingMap = [
            '0' => '0', '1' => '0.25rem', '2' => '0.5rem',
            '3' => '0.75rem', '4' => '1rem', '6' => '1.5rem',
            '8' => '2rem', '12' => '3rem', '16' => '4rem',
            '20' => '5rem', '24' => '6rem'
        ];

        return <<<CSS
        :root {
            --container-padding: {$spacingMap[$s['container_padding']]};
            --section-spacing: {$spacingMap[$s['section_spacing']]};
            --element-spacing: {$spacingMap[$s['element_spacing']]};
            --button-padding: {$spacingMap[$s['button_padding']]};
            --input-padding: {$spacingMap[$s['input_padding']]};
        }
        
        .container {
            padding: var(--container-padding);
        }
        
        .section {
            margin-bottom: var(--section-spacing);
        }
        
        .element {
            margin-bottom: var(--element-spacing);
        }
        
        .btn {
            padding: var(--button-padding);
        }
        
        input, textarea, select {
            padding: var(--input-padding);
        }
        
        .p-1 { padding: 0.25rem; }
        .p-2 { padding: 0.5rem; }
        .p-3 { padding: 0.75rem; }
        .p-4 { padding: 1rem; }
        .p-6 { padding: 1.5rem; }
        .p-8 { padding: 2rem; }
        CSS;
    }

    protected function generateBordersCss()
    {
        $b = $this->styles['borders'];
        $radiusMap = [
            'none' => '0', 'sm' => '0.125rem', 'md' => '0.375rem',
            'lg' => '0.5rem', 'xl' => '0.75rem', 'full' => '9999px'
        ];

        return <<<CSS
        :root {
            --border-width: {$b['border_width']}px;
            --border-radius: {$radiusMap[$b['border_radius']]};
            --border-color: {$b['border_color']};
            --border-style: {$b['border_style']};
        }
        
        .border {
            border-width: var(--border-width);
            border-style: var(--border-style);
            border-color: var(--border-color);
            border-radius: var(--border-radius);
        }
        
        .btn {
            border-radius: var(--border-radius);
        }
        
        .card {
            border-radius: var(--border-radius);
        }
        
        input, textarea, select {
            border-radius: var(--border-radius);
            border: var(--border-width) var(--border-style) var(--border-color);
        }
        
        .rounded-sm { border-radius: 0.125rem; }
        .rounded-md { border-radius: 0.375rem; }
        .rounded-lg { border-radius: 0.5rem; }
        .rounded-xl { border-radius: 0.75rem; }
        .rounded-full { border-radius: 9999px; }
        CSS;
    }

    protected function generateEffectsCss()
    {
        $e = $this->styles['effects'];
        $shadowMap = [
            'none' => 'none',
            'sm' => '0 1px 2px 0 rgb(0 0 0 / 0.05)',
            'md' => '0 4px 6px -1px rgb(0 0 0 / 0.1)',
            'lg' => '0 10px 15px -3px rgb(0 0 0 / 0.1)',
            'xl' => '0 20px 25px -5px rgb(0 0 0 / 0.1)',
            '2xl' => '0 25px 50px -12px rgb(0 0 0 / 0.25)'
        ];

        $transition = "all {$e['transition_speed']}ms ease-in-out";

        return <<<CSS
        :root {
            --shadow-intensity: {$shadowMap[$e['shadow_intensity']]};
            --transition-speed: {$e['transition_speed']}ms;
            --opacity: {$e['opacity']}%;
        }
        
        .shadow {
            box-shadow: var(--shadow-intensity);
        }
        
        .card {
            box-shadow: var(--shadow-intensity);
            transition: var(--transition-speed) ease-in-out;
        }
        
        .btn {
            transition: var(--transition-speed) ease-in-out;
        }
        
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.1);
        }
        
        .hover-scale:hover {
            transform: scale(1.05);
        }
        
        .hover-glow:hover {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
        }
        
        .hover-border:hover {
            border-color: var(--primary-color);
        }
        
        .hover-fade:hover {
            opacity: 0.8;
        }
        
        .opacity-50 { opacity: 0.5; }
        .opacity-75 { opacity: 0.75; }
        .opacity-90 { opacity: 0.9; }
        .opacity-100 { opacity: 1; }
        CSS;
    }

    protected function generateCustomCss()
    {
        $css = [];
        foreach ($this->styles['custom'] as $className => $styles) {
            $classCss = [];
            foreach ($styles as $property => $value) {
                if ($value) {
                    $classCss[] = "{$property}: {$value};";
                }
            }
            if (!empty($classCss)) {
                $css[] = ".{$className} {\n  " . implode("\n  ", $classCss) . "\n}";
            }
        }
        return implode("\n\n", $css);
    }

    public function applyStyles()
    {
        // Incluir custom CSS nos estilos
        if (!empty($this->customCss)) {
            $this->styles['custom_css'] = $this->customCss;
        }
        
        $css = $this->generateCss();
        $this->dispatch('stylesApplied', $this->styles, $css);
    }

    public function updatedCustomCss()
    {
        // Atualizar automaticamente quando o CSS customizado for alterado
        $this->styles['custom_css'] = $this->customCss;
    }

    public function updated($property, $value)
    {
        // Atualizar automaticamente quando qualquer propriedade for alterada
        if (str_starts_with($property, 'styles.')) {
            $this->dispatch('stylesUpdated', $this->styles);
        }
    }

    public function render()
    {
        return view('pagebuilder::livewire.advanced-style-editor');
    }
}