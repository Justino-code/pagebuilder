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
        ],
        'spacing' => [
            'container_padding' => '6',
            'section_spacing' => '8',
            'element_spacing' => '4',
            'button_padding' => '3',
        ],
        'borders' => [
            'border_width' => '1',
            'border_radius' => 'md',
            'border_color' => '#e5e7eb',
        ],
        'effects' => [
            'shadow_intensity' => 'md',
            'hover_effect' => 'lift',
            'transition_speed' => '300',
        ],
        'custom' => []
    ];

    public $currentTab = 'global';
    public $customClassName = '';
    public $customStyles = [];
    public $selectedElement = null;

    public $fontOptions = [
        'inter' => 'Inter', 'roboto' => 'Roboto', 'open-sans' => 'Open Sans',
        'poppins' => 'Poppins', 'montserrat' => 'Montserrat', 'system' => 'System'
    ];

    public $sizeOptions = [
        'xs' => 'Extra Small', 'sm' => 'Small', 'base' => 'Base', 
        'lg' => 'Large', 'xl' => 'Extra Large', '2xl' => '2X Large'
    ];

    public $spacingOptions = [
        '0' => 'None', '1' => 'XS', '2' => 'Small', '3' => 'Medium',
        '4' => 'Large', '6' => 'XL', '8' => '2XL', '12' => '3XL'
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
        'glow' => 'Glow', 'border' => 'Border Highlight'
    ];

    protected $listeners = ['elementSelected' => 'selectElement'];

    public function mount($initialStyles = [])
    {
        $this->styles = array_merge_recursive($this->styles, $initialStyles);
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
            'system' => 'system-ui, -apple-system, sans-serif'
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
        CSS;
    }

    protected function generateTypographyCss()
    {
        $t = $this->styles['typography'];
        $sizes = [
            'xs' => '0.75rem', 'sm' => '0.875rem', 'base' => '1rem',
            'lg' => '1.125rem', 'xl' => '1.25rem', '2xl' => '1.5rem'
        ];

        return <<<CSS
        h1, h2, h3, h4, h5, h6 {
            font-weight: 600;
            line-height: 1.3;
        }
        
        h1 { font-size: {$sizes['2xl']}; }
        h2 { font-size: {$sizes['xl']}; }
        h3 { font-size: {$sizes['lg']}; }
        
        body {
            font-size: {$sizes[$t['body_size']]};
        }
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
        $css = $this->generateCss();
        $this->dispatch('stylesApplied', $this->styles, $css);
    }

    public function render()
    {
        return view('pagebuilder::livewire.advanced-style-editor');
    }
}