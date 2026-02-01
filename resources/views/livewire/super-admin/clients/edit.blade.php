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
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Editar Cliente</h1>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $tenant->name }}</p>
                    </div>
                </div>
            </div>

            <!-- Estadísticas del Cliente -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow-lg p-6 mb-6 text-white">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm opacity-80">Jugadores</p>
                        <p class="text-2xl font-bold">{{ number_format($tenant->players()->count()) }}</p>
                    </div>
                    <div>
                        <p class="text-sm opacity-80">Saldo Total</p>
                        <p class="text-2xl font-bold">${{ number_format($tenant->players()->sum('balance'), 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm opacity-80">Transacciones</p>
                        <p class="text-2xl font-bold">{{ number_format($tenant->transactions()->count()) }}</p>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div x-data="{ activeTab: 'general' }" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                
                <!-- Tab Navigation -->
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="flex -mb-px overflow-x-auto">
                        <button @click="activeTab = 'general'" :class="activeTab === 'general' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'" class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                            ⚙️ General
                        </button>
                        <button @click="activeTab = 'subscription'" :class="activeTab === 'subscription' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'" class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                            💰 Suscripción
                        </button>
                        <button @click="activeTab = 'admin'" :class="activeTab === 'admin' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'" class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                            👤 Administrador
                        </button>
                        <button @click="activeTab = 'addons'" :class="activeTab === 'addons' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'" class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                            🧩 Add-ons
                        </button>
                    </nav>
                </div>

                <form wire:submit="save" class="p-6">
                    <!-- Tab: General -->
                    <div x-show="activeTab === 'general'" x-cloak>
                        @include('livewire.super-admin.clients.edit.tab-general')
                    </div>

                    <!-- Tab: Suscripción -->
                    <div x-show="activeTab === 'subscription'" x-cloak>
                        @include('livewire.super-admin.clients.edit.tab-subscription')
                    </div>

                    <!-- Tab: Administrador -->
                    <div x-show="activeTab === 'admin'" x-cloak>
                        @include('livewire.super-admin.clients.edit.tab-admin')
                    </div>

                    <!-- Tab: Add-ons -->
                    <div x-show="activeTab === 'addons'" x-cloak>
                        @include('livewire.super-admin.clients.edit.tab-addons')
                    </div>

                    <!-- Estado + Botones (siempre visibles) -->
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Cliente activo</span>
                            </label>
                            <div class="flex gap-3">
                                <a href="{{ route('super-admin.clients.index') }}" wire:navigate class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600 transition">
                                    Cancelar
                                </a>
                                <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                                    Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal DNS -->
    @if($showDnsInstructions)
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-8 max-w-2xl w-full mx-4 max-h-screen overflow-y-auto">
            <h3 class="text-lg font-bold mb-4 text-green-600 dark:text-green-400">✅ Dominio Actualizado</h3>
            <p class="mb-4 text-gray-700 dark:text-gray-300">El cliente debe configurar estos registros DNS:</p>
            <pre class="bg-gray-100 dark:bg-gray-900 p-4 rounded text-sm overflow-x-auto whitespace-pre-wrap text-gray-800 dark:text-gray-200">{{ $dnsInstructions }}</pre>
            <div class="mt-6 flex justify-end">
                <button wire:click="$set('showDnsInstructions', false)" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    Entendido
                </button>
            </div>
        </div>
    </div>
    @endif
</div>