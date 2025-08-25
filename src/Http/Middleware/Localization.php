<?php

namespace Justino\PageBuilder\Http\Middleware;

use Closure;
use Justino\PageBuilder\Helpers\Translator;

class Localization
{
    public function handle($request, Closure $next)
    {
        // Verificar se o idioma está na URL
        if ($request->has('lang')) {
            $locale = $request->get('lang');
            Translator::setLocale($locale);
        }
        // Verificar se o idioma está na sessão
        elseif (session()->has('pagebuilder_locale')) {
            app()->setLocale(session()->get('pagebuilder_locale'));
        }
        // Usar idioma padrão do aplicativo
        else {
            app()->setLocale(config('app.locale', 'pt'));
        }
        
        return $next($request);
    }
}