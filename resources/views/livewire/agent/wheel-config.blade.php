<div>
    <div class="space-y-6">
    {{-- Mensajes flash --}}
    @if(session()->has('success'))
        <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    @if(session()->has('error'))
        <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    {{-- Configuración General --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Configuración General</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">Ruleta Activa</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Los jugadores pueden girar la ruleta</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model.live="is_active" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-500 peer-checked:bg-blue-600"></div>
                </label>
            </div>

            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <label class="block font-medium text-gray-900 dark:text-white mb-2">Giros por día</label>
                <select wire:model.live="daily_limit" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                    @for($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}">{{ $i }} giro{{ $i > 1 ? 's' : '' }} por día</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>

    {{-- Segmentos/Premios --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Premios de la Ruleta</h3>
                <p class="text-sm {{ $totalProbability === 100 ? 'text-green-600' : 'text-red-600' }}">
                    Probabilidad total: {{ $totalProbability }}% 
                    @if($totalProbability !== 100)
                        <span class="font-bold">(debe ser 100%)</span>
                    @else
                        ✓
                    @endif
                </p>
            </div>
            <button wire:click="openAddSegment" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                + Agregar Premio
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 dark:text-gray-300">#</th>
                        <th class="px-4 py-3 text-left text-gray-700 dark:text-gray-300">Color</th>
                        <th class="px-4 py-3 text-left text-gray-700 dark:text-gray-300">Etiqueta</th>
                        <th class="px-4 py-3 text-left text-gray-700 dark:text-gray-300">Tipo</th>
                        <th class="px-4 py-3 text-left text-gray-700 dark:text-gray-300">Monto</th>
                        <th class="px-4 py-3 text-left text-gray-700 dark:text-gray-300">Probabilidad</th>
                        <th class="px-4 py-3 text-right text-gray-700 dark:text-gray-300">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($segments as $index => $segment)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $index + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="w-8 h-8 rounded-full border-2 border-gray-300" style="background-color: {{ $segment['color'] ?? '#ccc' }}"></div>
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $segment['label'] }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($segment['type'] === 'cash') bg-green-100 text-green-800
                                    @elseif($segment['type'] === 'bonus') bg-purple-100 text-purple-800
                                    @elseif($segment['type'] === 'free_spin') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $types[$segment['type']] ?? $segment['type'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-900 dark:text-white">
                                @if($segment['amount'] > 0)
                                    ${{ number_format($segment['amount'], 0) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-20 bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $segment['probability'] }}%"></div>
                                    </div>
                                    <span class="text-gray-900 dark:text-white">{{ $segment['probability'] }}%</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button wire:click="editSegment({{ $index }})" class="text-blue-600 hover:text-blue-800 mr-2">Editar</button>
                                <button wire:click="deleteSegment({{ $index }})" wire:confirm="¿Eliminar este premio?" class="text-red-600 hover:text-red-800">Eliminar</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Botón Guardar --}}
    <div class="flex justify-end">
        <button wire:click="saveConfig" 
                @if($totalProbability !== 100) disabled @endif
                class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
            Guardar Configuración
        </button>
    </div>

    {{-- Modal Segmento --}}
    @if($showSegmentModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                {{ $editingIndex !== null ? 'Editar Premio' : 'Agregar Premio' }}
            </h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de Premio</label>
                    <select wire:model.live="seg_type" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Etiqueta (texto visible)</label>
                    <input type="text" wire:model="seg_label" placeholder="Ej: $500" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                </div>

                @if(in_array($seg_type, ['cash', 'bonus']))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Monto ($)</label>
                    <input type="number" wire:model="seg_amount" min="0" step="1" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Probabilidad (%)</label>
                    <input type="number" wire:model="seg_probability" min="1" max="100" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color</label>
                    <input type="color" wire:model="seg_color" class="w-full h-10 rounded-lg cursor-pointer">
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button wire:click="closeModal" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    Cancelar
                </button>
                <button wire:click="saveSegment" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Guardar
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
</div>
