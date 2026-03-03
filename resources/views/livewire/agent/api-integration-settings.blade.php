<div class="max-w-4xl mx-auto">
    {{-- Mensajes flash --}}
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Integración API con Plataforma de Juego</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Configurá la conexión con tu plataforma de casino externa.</p>
        </div>

        <div class="p-6 space-y-6">

            {{-- Toggle Activar --}}
            <div class="flex items-center justify-between">
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Integración activa</label>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Las acciones se enviarán automáticamente a la API externa.</p>
                </div>
                <button type="button" wire:click="$toggle('enabled')"
                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $enabled ? 'bg-green-500' : 'bg-gray-300' }}">
                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $enabled ? 'translate-x-6' : 'translate-x-1' }}"></span>
                </button>
            </div>

            <hr class="border-gray-200 dark:border-gray-700">

            {{-- URL Base --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">URL Base de la API</label>
                <input type="url" wire:model="base_url" placeholder="https://api.casino.com"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                @error('base_url') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Tipo de Autenticación --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipo de Autenticación</label>
                <select wire:model="auth_type"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="token_body">Token en Body</option>
                    <option value="api_key">API Key</option>
                    <option value="bearer">Bearer Token</option>
                    <option value="basic">Basic Auth</option>
                    <option value="cookie_session">Cookie Session (Login automático)</option>
                </select>
            </div>

            {{-- Credenciales --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    @if($auth_type === 'api_key') API Key
                    @elseif($auth_type === 'bearer') Bearer Token
                    @else Usuario:Contraseña
                    @endif
                </label>
                <input type="password" wire:model="auth_credentials"
                    placeholder="{{ $auth_type === 'basic' ? 'usuario:contraseña' : 'Tu clave o token' }}"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                @error('auth_credentials') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Login automático (solo cookie_session) --}}
            @if($auth_type === 'cookie_session')
            <div class="p-4 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 space-y-4">
                <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">⚠️ Configuración de Login Automático</p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">URL de Login</label>
                    <input type="url" wire:model="extra_login_url" placeholder="https://agents.ganamosnet.org/login"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Usuario de Login</label>
                    <input type="text" wire:model="extra_login_username" placeholder="usuario"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contraseña de Login</label>
                    <input type="password" wire:model="extra_login_password" placeholder="contraseña"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
            </div>
            @endif

            {{-- Botón Test Conexión --}}
            <div>
                <button type="button" wire:click="testConnection" wire:loading.attr="disabled"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 transition">
                    <span wire:loading.remove wire:target="testConnection">🔌 Probar Conexión</span>
                    <span wire:loading wire:target="testConnection">Probando...</span>
                </button>
                @if($testResult)
                    @php [$type, $msg] = explode(':', $testResult, 2); @endphp
                    <span class="ml-3 text-sm {{ $type === 'success' ? 'text-green-600' : 'text-red-600' }}">{{ $msg }}</span>
                @endif
            </div>

            <hr class="border-gray-200 dark:border-gray-700">

            {{-- Endpoints --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Endpoints (rutas relativas a la URL base)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400">Crear Usuario</label>
                        <input type="text" wire:model="endpoint_create_user" placeholder="/api/users"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400">Cambiar Contraseña</label>
                        <input type="text" wire:model="endpoint_update_password" placeholder="/api/users/password"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400">Desbloquear Usuario</label>
                        <input type="text" wire:model="endpoint_unlock_user" placeholder="/api/users/unlock"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400">Depósito</label>
                        <input type="text" wire:model="endpoint_deposit" placeholder="/api/wallet/deposit"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400">Retiro</label>
                        <input type="text" wire:model="endpoint_withdraw" placeholder="/api/wallet/withdraw"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm sm:text-sm">
                    </div>
                </div>
            </div>

            <hr class="border-gray-200 dark:border-gray-700">

            {{-- Mapeo de Campos --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Mapeo de Campos</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Si la API del casino usa nombres de campos distintos, configuralo acá.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400">Campo "username"</label>
                        <input type="text" wire:model="mapping_username" placeholder="username"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400">Campo "password"</label>
                        <input type="text" wire:model="mapping_password" placeholder="password"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400">Campo "amount"</label>
                        <input type="text" wire:model="mapping_amount" placeholder="amount"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm sm:text-sm">
                    </div>
                </div>
            </div>

            <hr class="border-gray-200 dark:border-gray-700">

            {{-- Webhook Secret --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Webhook Entrante</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Secret para validar las llamadas que recibas desde la plataforma externa.</p>
                <div class="flex items-center gap-3">
                    <input type="text" wire:model="webhook_secret" readonly
                        class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm sm:text-sm bg-gray-50 dark:bg-gray-600">
                    <button type="button" wire:click="generateWebhookSecret"
                        class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition">
                        🔄 Generar
                    </button>
                </div>
            </div>

            <hr class="border-gray-200 dark:border-gray-700">

            {{-- Configuración Extra --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">⚙️ Parámetros Extra de la API</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Datos adicionales que requiera la plataforma externa (IDs de operador, tokens, etc.)</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400">Parent ID / Operador ID</label>
                        <input type="text" wire:model="extra_parent_id" placeholder="ej: 29435"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400">Source ID</label>
                        <input type="text" wire:model="extra_source_id" placeholder="ID usuario que procesa"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400">Token API</label>
                        <input type="text" wire:model="extra_token" placeholder="Token de autenticación"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm sm:text-sm">
                    </div>
                </div>
            </div>

            <hr class="border-gray-200 dark:border-gray-700">

            {{-- Botón Guardar --}}
            <div class="flex justify-end">
                <button type="button" wire:click="save" wire:loading.attr="disabled"
                    class="inline-flex items-center px-6 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700 transition">
                    <span wire:loading.remove wire:target="save">💾 Guardar Configuración</span>
                    <span wire:loading wire:target="save">Guardando...</span>
                </button>
            </div>

        </div>
    </div>
    {{-- Link a logs --}}
    <div class="mt-6 text-center">
        <a href="{{ route('api-integration.logs') }}" wire:navigate class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
            📋 Ver historial de actividad API
        </a>
    </div>
</div>