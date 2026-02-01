<?php

namespace App\Livewire\Agent;

use App\Models\AgentApiIntegration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use App\Livewire\Traits\WithTenantContext;

class ApiIntegrationSettings extends Component
{
    use WithTenantContext;

    public bool $enabled = false;
    public string $base_url = '';
    public string $auth_type = 'api_key';
    public string $auth_credentials = '';
    public string $webhook_secret = '';

    // Endpoints
    public string $endpoint_create_user = '';
    public string $endpoint_update_password = '';
    public string $endpoint_unlock_user = '';
    public string $endpoint_deposit = '';
    public string $endpoint_withdraw = '';

    // Field mappings
    public string $mapping_username = 'username';
    public string $mapping_password = 'password';
    public string $mapping_amount = 'amount';

    public bool $testingConnection = false;
    public ?string $testResult = null;

    // Extra config
    public string $extra_parent_id = '';
    public string $extra_source_id = '';
    public string $extra_token = '';

    public function mount()
    {
        if (!auth()->user()->tenant?->hasAddon('api_integration')) {
            return $this->redirect(route('dashboard'), navigate: true);
        }

        $tenant = Auth::user()->tenant;
        $config = $tenant->apiIntegration;

        if ($config) {
            $this->enabled = $config->enabled;
            $this->base_url = $config->base_url ?? '';
            $this->auth_type = $config->auth_type ?? 'api_key';
            $this->auth_credentials = $config->auth_credentials ?? '';
            $this->webhook_secret = $config->webhook_secret ?? '';

            $endpoints = $config->endpoints ?? [];
            $this->endpoint_create_user = $endpoints['create_user'] ?? '';
            $this->endpoint_update_password = $endpoints['update_password'] ?? '';
            $this->endpoint_unlock_user = $endpoints['unlock_user'] ?? '';
            $this->endpoint_deposit = $endpoints['deposit'] ?? '';
            $this->endpoint_withdraw = $endpoints['withdraw'] ?? '';

            $mappings = $config->field_mappings ?? [];
            $this->mapping_username = $mappings['username'] ?? 'username';
            $this->mapping_password = $mappings['password'] ?? 'password';
            $this->mapping_amount = $mappings['amount'] ?? 'amount';

            $extraConfig = $config->extra_config ?? [];
            $this->extra_parent_id = $extraConfig['parent_id'] ?? '';
            $this->extra_source_id = $extraConfig['source_id'] ?? '';
            $this->extra_token = $extraConfig['token'] ?? '';
        }
    }

    public function rules(): array
    {
        return [
            'base_url' => ['required_if:enabled,true', 'nullable', 'url'],
            'auth_type' => ['required', 'in:api_key,bearer,basic,token_body'],
            'auth_credentials' => ['required_if:enabled,true', 'nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'base_url.required_if' => 'La URL base es obligatoria si la integración está activa.',
            'base_url.url' => 'La URL base debe ser una URL válida.',
            'auth_credentials.required_if' => 'Las credenciales son obligatorias si la integración está activa.',
        ];
    }

    public function save()
    {
        $this->validate();

        $tenant = Auth::user()->tenant;

        $endpoints = array_filter([
            'create_user' => $this->endpoint_create_user,
            'update_password' => $this->endpoint_update_password,
            'unlock_user' => $this->endpoint_unlock_user,
            'deposit' => $this->endpoint_deposit,
            'withdraw' => $this->endpoint_withdraw,
        ]);

        $fieldMappings = [
            'username' => $this->mapping_username ?: 'username',
            'password' => $this->mapping_password ?: 'password',
            'amount' => $this->mapping_amount ?: 'amount',
        ];

        AgentApiIntegration::updateOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'enabled' => $this->enabled,
                'base_url' => $this->base_url ?: null,
                'auth_type' => $this->auth_type,
                'auth_credentials' => $this->auth_credentials ?: null,
                'endpoints' => $endpoints,
                'field_mappings' => $fieldMappings,
                'webhook_secret' => $this->webhook_secret ?: null,
                'extra_config' => [
                    'parent_id' => $this->extra_parent_id,
                    'source_id' => $this->extra_source_id,
                    'token' => $this->extra_token,
                ],
            ]
        );

        activity()
            ->performedOn($tenant)
            ->causedBy(Auth::user())
            ->withProperties(['enabled' => $this->enabled])
            ->log('Configuración de integración API actualizada');

        session()->flash('success', 'Configuración de API guardada correctamente.');
    }

    public function generateWebhookSecret()
    {
        $this->webhook_secret = Str::random(40);
    }

    public function testConnection()
    {
        $this->testResult = null;

        if (empty($this->base_url)) {
            $this->testResult = 'error:Ingresá una URL base primero.';
            return;
        }

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)->get($this->base_url);
            $this->testResult = 'success:Conexión exitosa (HTTP ' . $response->status() . ')';
        } catch (\Exception $e) {
            $this->testResult = 'error:No se pudo conectar: ' . Str::limit($e->getMessage(), 100);
        }
    }

    public function render()
    {
        return view('livewire.agent.api-integration-settings');
    }
}