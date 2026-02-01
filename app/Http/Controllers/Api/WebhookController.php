<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AgentApiIntegration;
use App\Models\Player;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\ApiIntegrationLog;

class WebhookController extends Controller
{
    public function handle(Request $request, string $tenantSlug)
    {
        // Buscar tenant por slug
        $tenant = Tenant::where('slug', $tenantSlug)->where('is_active', true)->first();

        if (!$tenant) {
            return response()->json(['error' => 'Tenant not found'], 404);
        }

        // Buscar configuración API del tenant
        $config = AgentApiIntegration::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenant->id)
            ->where('enabled', true)
            ->first();

        if (!$config) {
            return response()->json(['error' => 'API integration not configured'], 404);
        }

        // Validar webhook secret
        $secret = $request->header('X-Webhook-Secret');

        if (!$config->webhook_secret || $secret !== $config->webhook_secret) {
            Log::warning('Webhook: Invalid secret', [
                'tenant_id' => $tenant->id,
                'ip' => $request->ip(),
            ]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Procesar datos recibidos
        try {
            $data = $request->all();
            $mappings = $config->field_mappings ?? [];

            // Aplicar mapeo inverso (campo externo → campo interno)
            $inverseMappings = array_flip($mappings);
            $username = $data[$mappings['username'] ?? 'username'] ?? null;
            $password = $data[$mappings['password'] ?? 'password'] ?? null;
            $name = $data['name'] ?? $username;
            $phone = $data['phone'] ?? null;
            $email = $data['email'] ?? null;

            if (!$username) {
                return response()->json(['error' => 'Username is required'], 422);
            }

            // Verificar si ya existe
            $existing = Player::withoutGlobalScope('tenant')
                ->where('tenant_id', $tenant->id)
                ->where('username', strtolower($username))
                ->first();

            if ($existing) {
                return response()->json([
                    'status' => 'exists',
                    'message' => 'Player already exists',
                    'player_id' => $existing->id,
                ], 200);
            }

            // Crear jugador
            $player = Player::withoutGlobalScope('tenant')->create([
                'tenant_id' => $tenant->id,
                'name' => $name,
                'username' => strtolower($username),
                'phone' => $phone ?? 'webhook-' . Str::random(8),
                'email' => $email,
                'password' => $password ? bcrypt($password) : bcrypt(Str::random(12)),
                'balance' => 0,
                'referral_code' => strtoupper(Str::random(8)),
                'status' => 'active',
                'casino_linked' => true,
            ]);

            Log::info('Webhook: Player created', [
                'tenant_id' => $tenant->id,
                'player_id' => $player->id,
                'username' => $player->username,
            ]);

            ApiIntegrationLog::withoutGlobalScope('tenant')->create([
                'tenant_id' => $tenant->id,
                'action' => 'create_user',
                'direction' => 'incoming',
                'endpoint_url' => $request->fullUrl(),
                'request_data' => $request->except(['password']),
                'response_data' => ['player_id' => $player->id, 'username' => $player->username],
                'http_status' => 201,
                'status' => 'success',
            ]);

            return response()->json([
                'status' => 'created',
                'player_id' => $player->id,
                'username' => $player->username,
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Webhook: Failed', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}