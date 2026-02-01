{{-- Tab General: Información básica, marca blanca y URL casino --}}
<div class="space-y-6">
    
    <!-- Información Básica -->
    <div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Información Básica</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Nombre -->
            <div class="md:col-span-2">
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Nombre del Cliente <span class="text-red-500">*</span>
                </label>
                <input type="text" wire:model="name" id="name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Subdominio -->
            <div>
                <label for="domain" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Subdominio <span class="text-red-500">*</span>
                    <span class="text-gray-400 text-xs">(obligatorio)</span>
                </label>
                <div class="flex rounded-md shadow-sm">
                    <input type="text" wire:model="domain" id="domain" class="flex-1 min-w-0 block w-full rounded-none rounded-l-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="cliente1">
                    <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 dark:bg-gray-600 dark:border-gray-600 dark:text-gray-300 text-sm">
                        .{{ config('app.domain') }}
                    </span>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    El cliente accede en: <strong>{{ $domain ?: 'subdominio' }}.{{ config('app.domain') }}</strong>
                </p>
                @error('domain') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Dominio Personalizado -->
            <div class="border-t pt-6 dark:border-gray-700">
                <label for="custom_domain" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Dominio Personalizado 
                    <span class="text-gray-400 text-xs">(opcional)</span>
                </label>
                <input type="text" wire:model="custom_domain" id="custom_domain" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="www.ejemplo.com">
                @if($tenant->custom_domain)
                    <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                        ✓ Actualmente usando: <strong>{{ $tenant->custom_domain }}</strong>
                    </p>
                @endif
                @if($custom_domain)
                <div class="mt-2 text-sm bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded p-3">
                    <p class="font-bold text-green-800 dark:text-green-300 mb-2">📋 INSTRUCCIONES DNS PARA: {{ $custom_domain }}</p>
                    <p class="text-green-700 dark:text-green-400 mb-2">El cliente debe configurar estos registros DNS:</p>
                    <div class="font-mono text-xs space-y-2 text-green-700 dark:text-green-400">
                        <div>
                            <p class="font-bold">REGISTRO A:</p>
                            <p>Tipo: A | Host: @ | Valor: {{ config('app.server_ip', 'XXX.XXX.XXX.XXX') }} | TTL: 3600</p>
                        </div>
                        <div>
                            <p class="font-bold">REGISTRO A (WWW):</p>
                            <p>Tipo: A | Host: www | Valor: {{ config('app.server_ip', 'XXX.XXX.XXX.XXX') }} | TTL: 3600</p>
                        </div>
                    </div>
                    <div class="mt-3 pt-2 border-t border-green-300 dark:border-green-700 text-green-600 dark:text-green-500 text-xs">
                        <p>• La propagación DNS puede tardar hasta 48 horas</p>
                        <p>• Subdominio de respaldo: {{ $domain }}.{{ config('app.domain') }}</p>
                    </div>
                </div>
                @else
                <div class="mt-2 text-sm bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded p-3">
                    <p class="font-medium text-blue-800 dark:text-blue-300">ℹ️ Dominio Personalizado:</p>
                    <ul class="list-disc list-inside mt-2 space-y-1 text-blue-700 dark:text-blue-400">
                        <li>Permite que el cliente use su propio dominio</li>
                        <li>El cliente debe configurar sus registros DNS</li>
                        <li>El subdominio siempre estará disponible como respaldo</li>
                    </ul>
                </div>
                @endif
                @error('custom_domain') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    <!-- Marca Blanca -->
    <div class="border-t pt-6 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">🎨 Marca Blanca</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Color Primario -->
            <div>
                <label for="primary_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color Primario</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="primary_color" id="primary_color" class="h-10 w-20 rounded border-gray-300 cursor-pointer">
                    <input type="text" wire:model="primary_color" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                @error('primary_color') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Color Secundario -->
            <div>
                <label for="secondary_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color Secundario</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="secondary_color" id="secondary_color" class="h-10 w-20 rounded border-gray-300 cursor-pointer">
                    <input type="text" wire:model="secondary_color" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                @error('secondary_color') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Logo Actual -->
            @if($current_logo_url)
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Logo Actual</label>
                    <div class="flex items-center gap-4">
                        <img src="{{ $current_logo_url }}" alt="Logo actual" class="h-16 rounded border border-gray-300">
                        <button type="button" wire:click="removeLogo" class="text-red-600 hover:text-red-800 text-sm font-medium">Eliminar logo</button>
                    </div>
                </div>
            @endif

            <!-- Nuevo Logo -->
            <div class="md:col-span-2">
                <label for="logo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ $current_logo_url ? 'Cambiar Logo' : 'Subir Logo' }}
                </label>
                <input type="file" wire:model="logo" id="logo" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                @error('logo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                @if ($logo)
                    <div class="mt-2">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Vista previa del nuevo logo:</p>
                        <img src="{{ $logo->temporaryUrl() }}" alt="Preview" class="h-20 rounded">
                    </div>
                @endif
            </div>

            <!-- Preview de Colores -->
            <div class="md:col-span-2">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Vista previa de colores:</p>
                <div class="flex items-center gap-4">
                    <div class="px-6 py-3 rounded-md text-white font-medium" style="background-color: {{ $primary_color }}">Color Primario</div>
                    <div class="px-6 py-3 rounded-md text-white font-medium" style="background-color: {{ $secondary_color }}">Color Secundario</div>
                </div>
            </div>
        </div>
    </div>

    <!-- URL Casino -->
    <div class="border-t pt-6 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">🎰 Configuración del Casino</h3>
        <div>
            <label for="casino_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">URL del Casino</label>
            <input type="url" wire:model="casino_url" id="casino_url" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="https://casino.ejemplo.com">
            @error('casino_url') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                URL de la plataforma de juego externa. Los jugadores verán el botón "Ir al Casino" con este enlace.
            </p>
        </div>
    </div>
</div>