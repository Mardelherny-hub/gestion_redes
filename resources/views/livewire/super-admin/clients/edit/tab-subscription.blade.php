{{-- Tab Suscripción: Tipo, cuota, fichas, precio --}}
<div class="space-y-6">

    <!-- Tipo de Suscripción -->
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
            Tipo de Suscripción *
        </label>
        <div class="grid grid-cols-2 gap-4">
            <!-- Opción Prepago -->
            <label class="relative flex cursor-pointer rounded-lg border {{ $subscription_type === 'prepaid' ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600' }} p-4 hover:border-blue-500 transition">
                <input type="radio" wire:model.live="subscription_type" value="prepaid" class="sr-only">
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-lg font-semibold text-gray-900 dark:text-white">💎 Prepago (Fichas)</span>
                        @if($subscription_type === 'prepaid')
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Cliente compra fichas. Cada depósito aprobado = -1 ficha.
                    </p>
                </div>
            </label>

            <!-- Opción Mensual -->
            <label class="relative flex cursor-pointer rounded-lg border {{ $subscription_type === 'monthly' ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600' }} p-4 hover:border-blue-500 transition">
                <input type="radio" wire:model.live="subscription_type" value="monthly" class="sr-only">
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-lg font-semibold text-gray-900 dark:text-white">📅 Mensual (Abono)</span>
                        @if($subscription_type === 'monthly')
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Pago fijo mensual. Transacciones ilimitadas.
                    </p>
                </div>
            </label>
        </div>
        @error('subscription_type')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Campos condicionales según tipo -->
    @if($subscription_type === 'monthly')
        <div>
            <label for="monthly_fee" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Cuota Mensual (USD) *
            </label>
            <div class="relative">
                <span class="absolute left-3 top-2.5 text-gray-500 dark:text-gray-400">$</span>
                <input type="number" id="monthly_fee" wire:model="monthly_fee" step="0.01" class="pl-8 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="0.00">
            </div>
            @error('monthly_fee')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Saldo Actual de Fichas -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Saldo Actual de Fichas
                </label>
                <div class="px-4 py-3 bg-gray-100 dark:bg-gray-700 rounded-md">
                    <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($chips_balance) }}</span>
                    <span class="text-sm text-gray-600 dark:text-gray-400 ml-2">fichas</span>
                </div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    💡 Para cargar fichas, usa el botón 💎 en la lista de clientes
                </p>
            </div>

            <!-- Precio por Ficha -->
            <div>
                <label for="chip_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Precio por Ficha (USD) *
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-500 dark:text-gray-400">$</span>
                    <input type="number" id="chip_price" wire:model="chip_price" step="0.01" class="pl-8 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="100.00">
                </div>
                @error('chip_price')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Precio que paga el cliente por cada ficha
                </p>
            </div>
        </div>
    @endif
</div>