<?php

namespace App\Services;

use App\Models\AgentApiIntegration;
use App\Models\ApiIntegrationLog;
use App\Models\Player;
use App\Models\Tenant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ApiIntegrationService
{
    protected AgentApiIntegration $config;
    protected ?Player $currentPlayer = null;

    public function __construct(AgentApiIntegration $config)
    {
        $this->config = $config;
    }

    public static function forTenant(Tenant $tenant): ?self
    {
        $config = $tenant->apiIntegration;

        if (!$config || !$config->enabled) {
            return null;
        }

        return new self($config);
    }

    /**
     * Crear usuario en la plataforma externa
     */
    public function createUser(Player $player, ?string $password): array
    {
        $extra = $this->config->extra_config ?? [];
        $mappings = $this->config->field_mappings ?? [];

        $data = [
            $mappings['username'] ?? 'username' => $player->username,
            $mappings['password'] ?? 'password' => $password ?? '',
        ];

        // Agregar parent_id si existe
        if (!empty($extra['parent_id'])) {
            $data['parent_id'] = $extra['parent_id'];
        }

        $this->currentPlayer = $player;

        $result = $this->sendRequest('create_user', $data, 'POST');

        // Guardar external_id si la respuesta lo trae
        if ($result['success'] && !empty($result['response'])) {
            $response = $result['response'];
            $externalId = $response['data']['id'] ?? $response['id'] ?? $response['player_id'] ?? null;

            if ($externalId) {
                $player->update(['external_id' => (string) $externalId]);
            }
        }

        return $result;
    }

    /**
     * Cambiar contraseña en la plataforma externa
     */
    public function updatePassword(Player $player, string $password): array
    {
        $mappings = $this->config->field_mappings ?? [];

        $data = [
            $mappings['username'] ?? 'username' => $player->username,
            $mappings['password'] ?? 'password' => $password,
        ];

        $this->currentPlayer = $player;

        return $this->sendRequest('update_password', $data, 'POST');
    }

    /**
     * Desbloquear usuario en la plataforma externa
     */
    public function unlockUser(Player $player): array
    {
        $mappings = $this->config->field_mappings ?? [];

        $data = [
            $mappings['username'] ?? 'username' => $player->username,
        ];

        $this->currentPlayer = $player;

        return $this->sendRequest('unlock_user', $data, 'POST');
    }

    /**
     * Depósito en la plataforma externa
     */
    public function deposit(Player $player, float $amount): array
    {
        $extra = $this->config->extra_config ?? [];
        $mappings = $this->config->field_mappings ?? [];

        $data = [
            $mappings['amount'] ?? 'amount' => $amount,
            'action' => 'add',
        ];

        // IDs para plataformas que los requieran
        if (!empty($extra['source_id'])) {
            $data['source_id'] = $extra['source_id'];
        }

        if ($player->external_id) {
            $data['destination_id'] = $player->external_id;
        }

        $this->currentPlayer = $player;

        return $this->sendRequest('deposit', $data, 'POST');
    }

    /**
     * Retiro en la plataforma externa
     */
    public function withdraw(Player $player, float $amount): array
    {
        $extra = $this->config->extra_config ?? [];
        $mappings = $this->config->field_mappings ?? [];

        $data = [
            $mappings['amount'] ?? 'amount' => $amount,
            'action' => 'out',
        ];

        if (!empty($extra['source_id'])) {
            $data['source_id'] = $extra['source_id'];
        }

        if ($player->external_id) {
            $data['destination_id'] = $player->external_id;
        }

        $this->currentPlayer = $player;

        return $this->sendRequest('withdraw', $data, 'POST');
    }

    /**
     * Enviar request a la API externa
     */
    protected function sendRequest(string $action, array $data, string $method = 'POST'): array
    {
        $endpoints = $this->config->endpoints ?? [];
        $endpoint = $endpoints[$action] ?? null;

        if (!$endpoint) {
            $this->logRequest($action, 'outgoing', null, $data, null, null, 'error', "Endpoint '{$action}' no configurado.");
            return ['success' => false, 'error' => "Endpoint '{$action}' no configurado."];
        }

        $url = rtrim($this->config->base_url, '/') . '/' . ltrim($endpoint, '/');

        // Reemplazar placeholders dinámicos en la URL
        if ($this->currentPlayer) {
            $url = str_replace('{external_id}', $this->currentPlayer->external_id ?? '', $url);
            $url = str_replace('{username}', $this->currentPlayer->username ?? '', $url);
        }

        // Si auth es token_body, inyectar el token en los datos
        if ($this->config->auth_type === 'token_body') {
            $extra = $this->config->extra_config ?? [];
            if (!empty($extra['token'])) {
                $data['token'] = $extra['token'];
            }
        }

        try {
            $request = Http::timeout(15)->retry(1, 500);
            $request = $this->applyAuth($request);

            $response = $method === 'GET'
                ? $request->get($url, $data)
                : $request->post($url, $data);

            $status = $response->successful() ? 'success' : 'error';

            $this->logRequest($action, 'outgoing', $url, $data, $response->json() ?? $response->body(), $response->status(), $status);

            return [
                'success' => $response->successful(),
                'http_status' => $response->status(),
                'response' => $response->json() ?? $response->body(),
            ];

        } catch (\Exception $e) {
            $errorStatus = str_contains($e->getMessage(), 'timed out') ? 'timeout' : 'error';

            $this->logRequest($action, 'outgoing', $url, $data, null, null, $errorStatus, $e->getMessage());

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Aplicar autenticación según el tipo configurado
     */
    protected function applyAuth($request)
    {
        $credentials = $this->config->auth_credentials;

        if ($this->config->auth_type === 'token_body') {
            return $request;
        }

        if ($this->config->auth_type === 'cookie_session') {
            $sessionToken = $this->getSessionToken();
            if ($sessionToken) {
                $domain = parse_url($this->config->base_url, PHP_URL_HOST);
                return $request->withCookies(['session' => $sessionToken], $domain);
            }
            return $request;
        }

        if (!$credentials) {
            return $request;
        }

        return match ($this->config->auth_type) {
            'bearer' => $request->withToken($credentials),
            'basic' => $request->withBasicAuth(...explode(':', $credentials, 2)),
            'api_key' => $request->withHeaders(['X-API-Key' => $credentials]),
            default => $request,
        };
    }

    /**
     * Obtener token de sesión via login automático (cookie_session)
     */
    protected function getSessionToken(): ?string
    {
        $extra = $this->config->extra_config ?? [];
        $loginUrl = $extra['login_url'] ?? null;
        $username = $extra['login_username'] ?? null;
        $password = $extra['login_password'] ?? null;

        if (!$loginUrl || !$username || !$password) {
            Log::error('Cookie session: faltan credenciales de login', [
                'tenant_id' => $this->config->tenant_id,
            ]);
            return null;
        }

        // Cache del token por tenant (24 horas)
        $cacheKey = "api_session_token_{$this->config->tenant_id}";

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($loginUrl, $username, $password) {
            try {
                $response = Http::timeout(15)->post($loginUrl, [
                    'username' => $username,
                    'password' => $password,
                ]);

                if ($response->successful()) {
                    $cookies = $response->cookies();
                    $sessionCookie = $cookies->getCookieByName('session');

                    if ($sessionCookie) {
                        return $sessionCookie->getValue();
                    }
                }

                Log::error('Cookie session: login fallido', [
                    'tenant_id' => $this->config->tenant_id,
                    'http_status' => $response->status(),
                    'response' => $response->json(),
                ]);

                return null;
            } catch (\Exception $e) {
                Log::error('Cookie session: error en login', [
                    'tenant_id' => $this->config->tenant_id,
                    'error' => $e->getMessage(),
                ]);
                return null;
            }
        });
    }

    /**
     * Registrar log de la operación
     */
    protected function logRequest(string $action, string $direction, ?string $url, ?array $requestData, $responseData, ?int $httpStatus, string $status, ?string $errorMessage = null): void
    {
        try {
            // No loguear tokens en request_data
            $safeData = $requestData;
            if (isset($safeData['token'])) {
                $safeData['token'] = '***';
            }
            if (isset($safeData['password'])) {
                $safeData['password'] = '***';
            }

            ApiIntegrationLog::withoutGlobalScope('tenant')->create([
                'tenant_id' => $this->config->tenant_id,
                'action' => $action,
                'direction' => $direction,
                'endpoint_url' => $url,
                'request_data' => $safeData,
                'response_data' => is_array($responseData) ? $responseData : ($responseData ? ['raw' => $responseData] : null),
                'http_status' => $httpStatus,
                'status' => $status,
                'error_message' => $errorMessage,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to save API integration log', ['error' => $e->getMessage()]);
        }
    }
}