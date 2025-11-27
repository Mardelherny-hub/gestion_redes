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

                {{-- Configuración de Ruleta --}}
                @livewire('agent.wheel-config-button')
            </div>
        </div>
    </div>
</x-layouts.app>