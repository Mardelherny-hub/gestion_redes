<div>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center mb-4">
                    <a href="{{ route('super-admin.clients.index') }}" wire:navigate class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Nuevo Cliente</h1>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Crea un nuevo cliente en la plataforma</p>
                    </div>
                </div>
            </div>

            <!-- Formulario -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <form wire:submit="save" class="p-6 space-y-6">
                    
                    <!-- Información Básica -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Información Básica</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Nombre -->
                            <div class="md:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Nombre del Cliente <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    wire:model.blur="name" 
                                    id="name"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="Ej: Cliente Demo"
                                >
                                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Dominio -->
                            <div>
                                <label for="domain" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Dominio <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="domain" 
                                    id="domain"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="Ej: demo"
                                >
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Será usado como: {{ $domain ?: 'demo' }}.tuapp.com</p>
                                @error('domain') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Base de Datos -->
                            <div>
                                <label for="database" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Base de Datos <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="database" 
                                    id="database"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="gestion_redes"
                                >
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Nombre de la base de datos PostgreSQL</p>
                                @error('database') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Marca Blanca -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Marca Blanca</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Color Primario -->
                            <div>
                                <label for="primary_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Color Primario
                                </label>
                                <div class="flex items-center gap-2">
                                    <input 
                                        type="color" 
                                        wire:model.live="primary_color" 
                                        id="primary_color"
                                        class="h-10 w-20 rounded border-gray-300 cursor-pointer"
                                    >
                                    <input 
                                        type="text" 
                                        wire:model="primary_color" 
                                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="#3B82F6"
                                    >
                                </div>
                                @error('primary_color') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Color Secundario -->
                            <div>
                                <label for="secondary_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Color Secundario
                                </label>
                                <div class="flex items-center gap-2">
                                    <input 
                                        type="color" 
                                        wire:model.live="secondary_color" 
                                        id="secondary_color"
                                        class="h-10 w-20 rounded border-gray-300 cursor-pointer"
                                    >
                                    <input 
                                        type="text" 
                                        wire:model="secondary_color" 
                                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="#10B981"
                                    >
                                </div>
                                @error('secondary_color') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Logo -->
                            <div class="md:col-span-2">
                                <label for="logo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Logo
                                </label>
                                <input 
                                    type="file" 
                                    wire:model="logo" 
                                    id="logo"
                                    accept="image/*"
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                >
                                @error('logo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                
                                @if ($logo)
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Vista previa:</p>
                                        <img src="{{ $logo->temporaryUrl() }}" alt="Preview" class="h-20 rounded">
                                    </div>
                                @endif
                            </div>

                            <!-- Preview de Colores -->
                            <div class="md:col-span-2">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Vista previa:</p>
                                <div class="flex items-center gap-4">
                                    <div class="px-6 py-3 rounded-md text-white font-medium" style="background-color: {{ $primary_color }}">
                                        Color Primario
                                    </div>
                                    <div class="px-6 py-3 rounded-md text-white font-medium" style="background-color: {{ $secondary_color }}">
                                        Color Secundario
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Estado -->
                    <div>
                        <label class="flex items-center">
                            <input 
                                type="checkbox" 
                                wire:model="is_active" 
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Cliente activo</span>
                        </label>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Los clientes inactivos no pueden acceder a la plataforma</p>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('super-admin.clients.index') }}" wire:navigate class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Cancelar
                        </a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Crear Cliente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>