<div>
    {{-- Filtros --}}
    <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-3">
        <select wire:model.live="filterAction" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm">
            <option value="">Todas las acciones</option>
            <option value="create_user">Crear Usuario</option>
            <option value="update_password">Cambiar Contraseña</option>
            <option value="unlock_user">Desbloquear</option>
            <option value="deposit">Depósito</option>
            <option value="withdraw">Retiro</option>
        </select>
        <select wire:model.live="filterStatus" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm">
            <option value="">Todos los estados</option>
            <option value="success">Exitoso</option>
            <option value="error">Error</option>
            <option value="timeout">Timeout</option>
        </select>
        <select wire:model.live="filterDirection" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm">
            <option value="">Todas las direcciones</option>
            <option value="outgoing">Saliente</option>
            <option value="incoming">Entrante</option>
        </select>
    </div>

    {{-- Tabla --}}
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Acción</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Dir.</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Estado</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">HTTP</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Detalle</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($logs as $log)
                        <tr x-data="{ expanded: false }" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                {{ $log->created_at->format('d/m H:i:s') }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                @php
                                    $labels = [
                                        'create_user' => 'Crear Usuario',
                                        'update_password' => 'Contraseña',
                                        'unlock_user' => 'Desbloquear',
                                        'deposit' => 'Depósito',
                                        'withdraw' => 'Retiro',
                                    ];
                                @endphp
                                {{ $labels[$log->action] ?? $log->action }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($log->direction === 'outgoing')
                                    <span class="text-blue-600 dark:text-blue-400">↗ Saliente</span>
                                @else
                                    <span class="text-purple-600 dark:text-purple-400">↙ Entrante</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($log->status === 'success')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">✓ OK</span>
                                @elseif($log->status === 'error')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">✗ Error</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">⏱ Timeout</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                {{ $log->http_status ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <button @click="expanded = !expanded" class="text-indigo-600 dark:text-indigo-400 hover:underline text-xs">
                                    <span x-show="!expanded">Ver más</span>
                                    <span x-show="expanded">Ocultar</span>
                                </button>
                            </td>
                        </tr>
                        <tr x-show="expanded" x-cloak>
                            <td colspan="6" class="px-4 py-3 bg-gray-50 dark:bg-gray-900">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                                    <div>
                                        <p class="font-semibold text-gray-700 dark:text-gray-300 mb-1">URL</p>
                                        <p class="text-gray-500 dark:text-gray-400 break-all">{{ $log->endpoint_url ?? '-' }}</p>
                                    </div>
                                    @if($log->error_message)
                                        <div>
                                            <p class="font-semibold text-red-600 mb-1">Error</p>
                                            <p class="text-red-500 break-all">{{ $log->error_message }}</p>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-semibold text-gray-700 dark:text-gray-300 mb-1">Request</p>
                                        <pre class="text-gray-500 dark:text-gray-400 whitespace-pre-wrap break-all bg-gray-100 dark:bg-gray-800 p-2 rounded">{{ json_encode($log->request_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-700 dark:text-gray-300 mb-1">Response</p>
                                        <pre class="text-gray-500 dark:text-gray-400 whitespace-pre-wrap break-all bg-gray-100 dark:bg-gray-800 p-2 rounded">{{ json_encode($log->response_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                No hay registros de actividad API.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $logs->links() }}
        </div>
    </div>
</div>