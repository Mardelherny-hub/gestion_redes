<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $subdomain = $this->getSubdomain($request);
        
        if (!$subdomain) {
            abort(404, 'No se pudo identificar el cliente');
        }

        // Buscar el tenant por domain exacto O por subdomain
        $tenant = Tenant::where('domain', $subdomain)
            ->orWhere('domain', 'like', $subdomain . '.%')
            ->where('is_active', true)
            ->first();

        if (!$tenant) {
            abort(404, 'Cliente no encontrado o inactivo');
        }

        // Guardar el tenant en el request para usarlo en toda la aplicación
        $request->attributes->set('current_tenant', $tenant);
        
        // También lo guardamos en config para fácil acceso
        config(['app.current_tenant' => $tenant]);

        // Compartir con todas las vistas
        view()->share('currentTenant', $tenant);

        return $next($request);
    }

    /**
     * Extraer el subdomain del request
     */
    private function getSubdomain(Request $request): ?string
    {
        $host = $request->getHost();
        
        // Si estás en localhost o una IP, retornar 'demo' por defecto para testing
        if ($host === 'localhost' || filter_var($host, FILTER_VALIDATE_IP)) {
            return 'demo'; // Cambiar según tu tenant de prueba
        }

        // Extraer subdomain (ej: demo.casinoredes.test -> demo)
        $parts = explode('.', $host);
        
        if (count($parts) > 2) {
            return $parts[0];
        }

        return null;
    }
}