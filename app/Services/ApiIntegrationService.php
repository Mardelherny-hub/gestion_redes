<?php

namespace App\Services;

use App\Models\AgentApiIntegration;
use App\Models\ApiIntegrationLog;
use App\Models\Player;
use App\Models\Tenant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiIntegrationService
{
    protected AgentApiIntegration $config;

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

        if (!$credentials || $this->config->auth_type === 'token_body') {
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