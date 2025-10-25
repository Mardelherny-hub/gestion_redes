<div>
    @if($isOpen && $transaction)
        <!-- Overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
             x-data="{ show: @entangle('isOpen') }"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            
            <!-- Modal -->
            <div class="bg-white dark:bg-gray-800 rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl"
                 @click.away="$wire.close()"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100">
                
                <!-- Header -->
                <div class="sticky top-0 bg-white dark:bg-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Aprobar Transacción</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">ID: #{{ $transaction->id }}</p>
                    </div>
                    <button wire:click="close" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6 space-y-6">
                    
                    <!-- Info del Jugador -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-3">JUGADOR</h4>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-gray-600 dark:text-gray-400">Nombre</p>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $transaction->player->name }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 dark:text-gray-400">Email</p>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $transaction->player->email }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 dark:text-gray-400">Teléfono</p>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $transaction->player->phone }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 dark:text-gray-400">Saldo Actual</p>
                                <p class="font-semibold text-green-600 dark:text-green-400">${{ number_format($transaction->player->balance, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Info de la Transacción -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-3">DETALLES DE LA TRANSACCIÓN</h4>
                        
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                @if($transaction->type === 'deposit')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        DEPÓSITO
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        RETIRO
                                    </span>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-bold text-gray-900 dark:text-white">${{ number_format($transaction->amount, 2) }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-gray-600 dark:text-gray-400">Fecha de Solicitud</p>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 dark:text-gray-400">Tiempo de Espera</p>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $transaction->created_at->diffForHumans() }}</p>
                            </div>
                        </div>

                        @if($transaction->notes)
                            <div class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900 dark:bg-opacity-20 rounded">
                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Datos adicionales</p>
                                <p class="text-sm text-gray-900 dark:text-white">{{ $transaction->notes }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Comprobante (solo para depósitos) -->
                    @if($transaction->type === 'deposit' && $transaction->proof_url)
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-3">COMPROBANTE</h4>
                            <div class="flex items-center space-x-4">
                                <img src="{{ $transaction->proof_url }}" 
                                     alt="Comprobante" 
                                     class="w-32 h-32 object-cover rounded border-2 border-gray-300 dark:border-gray-600 cursor-pointer hover:opacity-75"
                                     onclick="window.open('{{ $transaction->proof_url }}', '_blank')">
                                <div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">Click en la imagen para ampliar</p>
                                    <a href="{{ $transaction->proof_url }}" 
                                       target="_blank" 
                                       class="text-blue-600 dark:text-blue-400 text-sm hover:underline flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                        Abrir en nueva pestaña
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Notas adicionales (opcional) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Notas adicionales (opcional)
                        </label>
                        <textarea 
                            wire:model="notes"
                            rows="3"
                            placeholder="Agregar comentarios sobre esta aprobación..."
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"></textarea>
                    </div>

                    <!-- Advertencia -->
                    <div class="bg-green-50 dark:bg-green-900 dark:bg-opacity-20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                        <div class="flex gap-3">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="text-sm text-green-800 dark:text-green-200">
                                <p class="font-semibold mb-1">Al aprobar esta transacción:</p>
                                <ul class="list-disc list-inside space-y-1">
                                    @if($transaction->type === 'deposit')
                                        <li>Se agregará ${{ number_format($transaction->amount, 2) }} al saldo del jugador</li>
                                        <li>Nuevo saldo: ${{ number_format($transaction->player->balance + $transaction->amount, 2) }}</li>
                                    @else
                                        <li>Se descontará ${{ number_format($transaction->amount, 2) }} del saldo del jugador</li>
                                        <li>Nuevo saldo: ${{ number_format($transaction->player->balance - $transaction->amount, 2) }}</li>
                                    @endif
                                    <li>El jugador recibirá una notificación</li>
                                    <li>Esta acción no se puede deshacer</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Footer -->
                <div class="sticky bottom-0 bg-gray-50 dark:bg-gray-800 px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex gap-3">
                    <button 
                        type="button"
                        wire:click="close"
                        class="flex-1 px-6 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                        Cancelar
                    </button>
                    <button 
                        type="button"
                        wire:click="approve"
                        wire:loading.attr="disabled"
                        class="flex-1 px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition disabled:opacity-50 flex items-center justify-center gap-2">
                        <svg wire:loading.remove class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <svg wire:loading class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove>Aprobar Transacción</span>
                        <span wire:loading>Procesando...</span>
                    </button>
                </div>

            </div>
        </div>
    @endif
</div>