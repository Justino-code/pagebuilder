<?php

namespace Justino\PageBuilder\Http\Livewire;

use Livewire\Component;
use Justino\PageBuilder\Helpers\Translator;

class LanguageSelector extends Component
{
    public $currentLocale;
    public $availableLocales = [];
    
    public function mount()
    {
        $this->currentLocale = Translator::getCurrentLocale();
        $this->availableLocales = Translator::getAvailableLocales();
    }
    
    public function changeLocale($locale)
    {
        if (Translator::setLocale($locale)) {
            session()->put('pagebuilder_locale', $locale);
            $this->currentLocale = $locale;
            $this->emit('localeChanged', $locale);
        }
    }
    
    public function render()
    {
        return view('pagebuilder::livewire.language-selector');
    }
}