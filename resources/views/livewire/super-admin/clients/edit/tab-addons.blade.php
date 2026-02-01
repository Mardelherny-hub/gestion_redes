{{-- Tab Add-ons: Funcionalidades adicionales por tenant --}}
<div class="space-y-6">

    <div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">🧩 Add-ons</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Habilitar o deshabilitar funcionalidades adicionales para este cliente.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach(config('addons') as $key => $addon)
            <div class="flex items-center justify-between p-4 rounded-lg border {{ ($addons[$key] ?? false) ? 'border-green-300 bg-green-50 dark:border-green-700 dark:bg-green-900/20' : 'border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800' }} transition-colors">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">{{ $addon['icon'] }}</span>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white text-sm">{{ $addon['name'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $addon['description'] }}</p>
                    </div>
                </div>
                <button 
                    type="button"
                    wire:click="toggleAddon('{{ $key }}')"
                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ ($addons[$key] ?? false) ? 'bg-green-600' : 'bg-gray-300 dark:bg-gray-600' }}"
                >
                    <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition duration-200 ease-in-out {{ ($addons[$key] ?? false) ? 'translate-x-5' : 'translate-x-0' }}"></span>
                </button>
            </div>
        @endforeach
    </div>

    @if(collect($addons)->filter()->isEmpty())
        <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-4">
            No hay add-ons habilitados para este cliente.
        </p>
    @endif
</div>