<?php

namespace Justino\PageBuilder\Http\Livewire;

use Livewire\Component;
use Justino\PageBuilder\Helpers\Translator;

class StyleEditor extends Component
{
    public $styles = [
        'font_family' => 'inter',
        'primary_color' => '#3b82f6',
        'secondary_color' => '#64748b',
        'background_color' => '#ffffff',
        'text_color' => '#1f2937',
        'border_radius' => '0.375rem',
        'shadow' => 'md',
        'spacing' => 'normal'
    ];

    public $fontOptions = [
        'inter' => 'Inter (Modern)',
        'roboto' => 'Roboto (Clean)',
        'open-sans' => 'Open Sans (Friendly)',
        'poppins' => 'Poppins (Elegant)',
        'system' => 'System Default'
    ];

    public $shadowOptions = [
        'none' => 'No Shadow',
        'sm' => 'Small Shadow',
        'md' => 'Medium Shadow',
        'lg' => 'Large Shadow',
        'xl' => 'Extra Large Shadow'
    ];

    public $spacingOptions = [
        'compact' => 'Compact',
        'normal' => 'Normal',
        'comfortable' => 'Comfortable',
        'spacious' => 'Spacious'
    ];

    public $borderRadiusOptions = [
        'none' => 'No Radius',
        'sm' => 'Small',
        'md' => 'Medium',
        'lg' => 'Large',
        'full' => 'Full Rounded'
    ];

    protected $listeners = ['stylesUpdated' => 'updateStyles'];

    public function mount($initialStyles = [])
    {
        $this->styles = array_merge($this->styles, $initialStyles);
    }

    public function updateStyles($styles)
    {
        $this->styles = array_merge($this->styles, $styles);
    }

    public function generateCss()
    {
        $css = [];
        
        // Font family
        $css[] = $this->getFontFamilyCss();
        
        // Colors
        $css[] = $this->getColorCss();
        
        // Spacing
        $css[] = $this->getSpacingCss();
        
        // Border radius
        $css[] = $this->getBorderRadiusCss();
        
        // Shadows
        $css[] = $this->getShadowCss();

        dd($css);

        return implode("\n", array_filter($css));
    }

    protected function getFontFamilyCss()
    {
        $fonts = [
            'inter' => 'Inter, system-ui, sans-serif',
            'roboto' => 'Roboto, system-ui, sans-serif',
            'open-sans' => '"Open Sans", system-ui, sans-serif',
            'poppins' => 'Poppins, system-ui, sans-serif',
            'system' => 'system-ui, -apple-system, sans-serif'
        ];

        return <<<CSS
        :root {
            --font-family: {$fonts[$this->styles['font_family']]};
        }
        
        body {
            font-family: var(--font-family);
        }
        CSS;
    }

    protected function getColorCss()
    {
        return <<<CSS
        :root {
            --primary-color: {$this->styles['primary_color']};
            --secondary-color: {$this->styles['secondary_color']};
            --background-color: {$this->styles['background_color']};
            --text-color: {$this->styles['text_color']};
        }
        
        .bg-primary { background-color: var(--primary-color); }
        .text-primary { color: var(--primary-color); }
        .border-primary { border-color: var(--primary-color); }
        
        .bg-secondary { background-color: var(--secondary-color); }
        .text-secondary { color: var(--secondary-color); }
        
        body { 
            background-color: var(--background-color);
            color: var(--text-color);
        }
        CSS;
    }

    protected function getSpacingCss()
    {
        $spacing = [
            'compact' => '0.5rem',
            'normal' => '1rem',
            'comfortable' => '1.5rem',
            'spacious' => '2rem'
        ];

        return <<<CSS
        :root {
            --spacing: {$spacing[$this->styles['spacing']]};
        }
        
        .padded { padding: var(--spacing); }
        .spaced-y > * + * { margin-top: var(--spacing); }
        .spaced-x > * + * { margin-left: var(--spacing); }
        CSS;
    }

    protected function getBorderRadiusCss()
    {
        $radius = [
            'none' => '0',
            'sm' => '0.125rem',
            'md' => '0.375rem',
            'lg' => '0.5rem',
            'full' => '9999px'
        ];

        return <<<CSS
        :root {
            --border-radius: {$radius[$this->styles['border_radius']]};
        }
        
        .rounded { border-radius: var(--border-radius); }
        .rounded-btn { border-radius: calc(var(--border-radius) * 2); }
        CSS;
    }

    protected function getShadowCss()
    {
        $shadows = [
            'none' => 'none',
            'sm' => '0 1px 2px 0 rgb(0 0 0 / 0.05)',
            'md' => '0 4px 6px -1px rgb(0 0 0 / 0.1)',
            'lg' => '0 10px 15px -3px rgb(0 0 0 / 0.1)',
            'xl' => '0 20px 25px -5px rgb(0 0 0 / 0.1)'
        ];

        return <<<CSS
        :root {
            --shadow: {$shadows[$this->styles['shadow']]};
        }
        
        .shadow { box-shadow: var(--shadow); }
        CSS;
    }

    public function applyStyles()
    {
        $this->dispatch('stylesApplied', $this->styles, $this->generateCss());
    }

    public function render()
    {
        return view('pagebuilder::livewire.style-editor');
    }
}