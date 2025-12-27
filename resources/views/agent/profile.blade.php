<x-layouts.app>
    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Configuración</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">Gestiona la configuración de tu casino</p>
            </div>

            <div class="space-y-6">
                {{-- Configuración WhatsApp y Casino --}}
                @livewire('agent.tenant-settings')

                {{-- Configuración de Bonos (ya existente) --}}
                @livewire('agent.bonus-settings')

                {{-- Enlace a Configuración de Ruleta --}}
                {{-- <a href="{{ route('wheel-config') }}" 
                   class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition duration-150 ease-in-out shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Configurar Ruleta
                </a> --}}
            </div>
        </div>
    </div>
</x-layouts.app>