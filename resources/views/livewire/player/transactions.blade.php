<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Mis Transacciones</h1>
        <p class="text-gray-400">Historial completo de cargas, retiros y bonos</p>
    </div>

    <!-- Filtros -->
    <div class="bg-gray-800 rounded-xl p-6 mb-6 border border-gray-700">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            
            <!-- Búsqueda -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Buscar</label>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search"
                    placeholder="Monto o ID..."
                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-gray-500">
            </div>

            <!-- Filtro por tipo -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Tipo</label>
                <select wire:model.live="typeFilter" 
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-gray-500">
                    <option value="all">Todos</option>
                    <option value="deposit">Depósitos</option>
                    <option value="withdrawal">Retiros</option>
                    <option value="bonus">Bonos</option>
                </select>
            </div>

            <!-- Filtro por estado -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Estado</label>
                <select wire:model.live="statusFilter" 
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-gray-500">
                    <option value="all">Todos</option>
                    <option value="pending">Pendientes</option>
                    <option value="completed">Completadas</option>
                    <option value="rejected">Rechazadas</option>
                </select>
            </div>

        </div>
    </div>

    <!-- Lista de Transacciones -->
    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
        @if($transactions->count() > 0)
            <div class="divide-y divide-gray-700">
                @foreach($transactions as $transaction)
                    <div class="p-6 hover:bg-gray-700/50 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <!-- Icono según tipo -->
                                <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0
                                    {{ $transaction->type == 'deposit' ? 'bg-green-500/20' : 
                                       ($transaction->type == 'withdrawal' ? 'bg-red-500/20' : 'bg-yellow-500/20') }}">
                                    @if($transaction->type == 'deposit')
                                        <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                                        </svg>
                                    @elseif($transaction->type == 'withdrawal')
                                        <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                                        </svg>
                                    @else
                                        <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                                        </svg>
                                    @endif
                                </div>

                                <div>
                                    <p class="font-semibold text-white text-lg">
                                        @if($transaction->type == 'deposit') Depósito
                                        @elseif($transaction->type == 'withdrawal') Retiro
                                        @elseif($transaction->type == 'bonus') Bono
                                        @else {{ ucfirst($transaction->type) }}
                                        @endif
                                    </p>
                                    <p class="text-sm text-gray-400">{{ $transaction->created_at->format('d/m/Y H:i') }}</p>
                                    <p class="text-xs text-gray-500 font-mono mt-1">ID: #{{ $transaction->id }}</p>
                                </div>
                            </div>

                            <div class="text-right">
                                <p class="text-2xl font-bold mb-2 {{ $transaction->type == 'deposit' || $transaction->type == 'bonus' ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $transaction->type == 'deposit' || $transaction->type == 'bonus' ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                                </p>
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-medium
                                    {{ $transaction->status == 'completed' ? 'bg-green-500/20 text-green-400' : 
                                       ($transaction->status == 'pending' ? 'bg-yellow-500/20 text-yellow-400' : 'bg-red-500/20 text-red-400') }}">
                                    @if($transaction->status == 'completed') Completada
                                    @elseif($transaction->status == 'pending') Pendiente
                                    @elseif($transaction->status == 'rejected') Rechazada
                                    @else {{ ucfirst($transaction->status) }}
                                    @endif
                                </span>

                                @if($transaction->notes)
                                    <p class="text-xs text-gray-500 mt-2 max-w-xs truncate">{{ $transaction->notes }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginación -->
            <div class="px-6 py-4 border-t border-gray-700">
                {{ $transactions->links() }}
            </div>

        @else
            <!-- Empty State -->
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <p class="text-gray-400 text-lg mb-2">No se encontraron transacciones</p>
                <p class="text-gray-500 text-sm">
                    @if($search || $typeFilter !== 'all' || $statusFilter !== 'all')
                        Intenta cambiar los filtros de búsqueda
                    @else
                        Realiza tu primera carga de saldo
                    @endif
                </p>
            </div>
        @endif
    </div>

</div>