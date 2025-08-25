<?php

namespace Justino\PageBuilder\Helpers;

class Translator
{
    public static function trans($key, $replace = [], $locale = null)
    {
        return trans('pagebuilder::' . $key, $replace, $locale);
    }
    
    public static function getAvailableLocales()
    {
        return [
            'en' => 'English',
            'pt' => 'Português',
            'es' => 'Español',
        ];
    }
    
    public static function getCurrentLocale()
    {
        return app()->getLocale();
    }
    
    public static function setLocale($locale)
    {
        if (array_key_exists($locale, self::getAvailableLocales())) {
            app()->setLocale($locale);
            return true;
        }
        
        return false;
    }
}