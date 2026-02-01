{{-- Tab Administrador: Datos admin y cambio de contraseña --}}
<div class="space-y-6">

    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">👤 Administrador del Cliente</h3>

    @php
        $admin = $tenant->users()->where('role', 'admin')->first();
    @endphp

    @if($admin)
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Nombre:</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $admin->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Email:</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $admin->email }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Último acceso:</p>
                    <p class="font-medium text-gray-900 dark:text-white">
                        {{ $admin->last_login_at ? $admin->last_login_at->diffForHumans() : 'Nunca' }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Estado:</p>
                    <p class="font-medium">
                        @if($admin->is_active)
                            <span class="text-green-600 dark:text-green-400">✓ Activo</span>
                        @else
                            <span class="text-red-600 dark:text-red-400">✗ Inactivo</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">🔐 Cambiar Contraseña</p>
                <div class="flex gap-2">
                    <input 
                        type="password" 
                        wire:model="admin_password" 
                        placeholder="Nueva contraseña (mín. 8 caracteres)"
                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm"
                    >
                    <button 
                        type="button"
                        wire:click="changeAdminPassword"
                        class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 text-sm font-medium"
                    >
                        Cambiar
                    </button>
                </div>
                @error('admin_password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
        </div>
    @else
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded p-4">
            <p class="text-yellow-800 dark:text-yellow-300">
                ⚠️ Este cliente no tiene un administrador asignado.
            </p>
        </div>
    @endif
</div>