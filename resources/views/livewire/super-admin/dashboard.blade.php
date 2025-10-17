<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Dashboard Super Admin</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Vista general de todos los clientes</p>
            </div>

            <!-- Métricas Principales -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                
                <!-- Total Clientes -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 p-3 rounded-md bg-indigo-100">
                                <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Clientes</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($totalClients) }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <span class="text-green-600 font-medium">{{ number_format($activeClients) }}</span> activos
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Jugadores -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 p-3 rounded-md bg-purple-100">
                                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Jugadores</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($totalPlayers) }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Todos los clientes</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Saldo Total -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 p-3 rounded-md bg-green-100">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Saldo Total</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white">${{ number_format($totalBalance, 2) }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">En el sistema</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transacciones Pendientes -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 p-3 rounded-md bg-yellow-100">
                                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pendientes</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($pendingTransactions) }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Transacciones</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actividad de Hoy -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Transacciones Hoy</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ number_format($totalTransactionsToday) }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Depósitos Hoy</h3>
                        <p class="text-3xl font-bold text-green-600">${{ number_format($totalDepositsToday, 2) }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Retiros Hoy</h3>
                        <p class="text-3xl font-bold text-red-600">${{ number_format($totalWithdrawalsToday, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Dos Columnas: Últimos Clientes + Top Clientes -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Últimos Clientes Creados -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Últimos Clientes Creados</h3>
                        <div class="space-y-3">
                            @forelse($recentClients as $client)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $client->name }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $client->domain }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full {{ $client->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $client->is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-gray-400">No hay clientes registrados</p>
                            @endforelse
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('super-admin.clients.index') }}" wire:navigate class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                Ver todos los clientes →
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Top Clientes por Saldo -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top Clientes por Saldo</h3>
                        <div class="space-y-3">
                            @forelse($topClients as $client)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $client->name }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $client->players_count ?? 0 }} jugadores</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-green-600">${{ number_format($client->players_sum_balance ?? 0, 2) }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-gray-400">No hay datos disponibles</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>