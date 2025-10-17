<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }

    /**
     * Handle an unauthenticated user.
     */
    protected function unauthenticated($request, array $guards)
    {
        // Verificar que el usuario autenticado pertenece al tenant actual
        // SOLO si NO es super admin
        if (auth()->check() && !auth()->user()->is_super_admin) {
            $currentTenant = $request->attributes->get('current_tenant');
            
            if ($currentTenant && auth()->user()->tenant_id !== $currentTenant->id) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
        }

        parent::unauthenticated($request, $guards);
    }
}