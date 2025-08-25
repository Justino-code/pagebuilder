<?php

namespace Justino\PageBuilder\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PageBuilderAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Verificar se o usuário está autenticado
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        // Verificar se o usuário tem permissão para acessar o page builder
        if (config('pagebuilder.auth.enabled', true)) {
            $allowedRoles = config('pagebuilder.auth.roles', ['admin']);
            $user = auth()->user();
            
            if (!empty($allowedRoles) && !$user->hasAnyRole($allowedRoles)) {
                abort(403, 'Unauthorized access to page builder.');
            }
        }
        
        return $next($request);
    }
}