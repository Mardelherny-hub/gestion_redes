<?php

namespace App\Livewire\Agent;

use App\Models\WheelConfig as WheelConfigModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class WheelConfig extends Component
{
    public bool $is_active = true;
    public int $daily_limit = 1;
    public array $segments = [];
    
    public bool $showSegmentModal = false;
    public ?int $editingIndex = null;
    
    // Campos del modal
    public string $seg_type = 'cash';
    public float $seg_amount = 0;
    public int $seg_probability = 10;
    public string $seg_label = '';
    public string $seg_color = '#22c55e';

    protected $rules = [
        'is_active' => 'boolean',
        'daily_limit' => 'required|integer|min:1|max:10',
        'segments' => 'required|array|min:2|max:12',
    ];

    public function mount()
    {
        $tenantId = Auth::user()->tenant_id;
        $config = WheelConfigModel::where('tenant_id', $tenantId)->first();
        
        if ($config) {
            $this->is_active = $config->is_active;
            $this->daily_limit = $config->daily_limit;
            $this->segments = $config->segments;
        } else {
            $this->segments = WheelConfigModel::getDefaultSegments();
        }
    }

    public function saveConfig()
    {
        // Validar que probabilidades sumen 100
        $totalProb = array_sum(array_column($this->segments, 'probability'));
        if ($totalProb !== 100) {
            session()->flash('error', "Las probabilidades deben sumar 100%. Actualmente suman {$totalProb}%");
            return;
        }

        $tenantId = Auth::user()->tenant_id;
        
        WheelConfigModel::updateOrCreate(
            ['tenant_id' => $tenantId],
            [
                'is_active' => $this->is_active,
                'daily_limit' => $this->daily_limit,
                'segments' => $this->segments,
            ]
        );

        session()->flash('success', 'Configuración de la ruleta guardada correctamente.');
    }

    public function openAddSegment()
    {
        $this->resetSegmentForm();
        $this->editingIndex = null;
        $this->showSegmentModal = true;
    }

    public function editSegment(int $index)
    {
        $segment = $this->segments[$index];
        $this->seg_type = $segment['type'];
        $this->seg_amount = $segment['amount'];
        $this->seg_probability = $segment['probability'];
        $this->seg_label = $segment['label'];
        $this->seg_color = $segment['color'] ?? '#22c55e';
        $this->editingIndex = $index;
        $this->showSegmentModal = true;
    }

    public function saveSegment()
    {
        $this->validate([
            'seg_type' => 'required|in:cash,bonus,free_spin,nothing',
            'seg_amount' => 'required|numeric|min:0',
            'seg_probability' => 'required|integer|min:1|max:100',
            'seg_label' => 'required|string|max:50',
            'seg_color' => 'required|string',
        ]);

        $segment = [
            'position' => $this->editingIndex !== null ? $this->editingIndex + 1 : count($this->segments) + 1,
            'type' => $this->seg_type,
            'amount' => (float) $this->seg_amount,
            'probability' => (int) $this->seg_probability,
            'label' => $this->seg_label,
            'color' => $this->seg_color,
        ];

        if ($this->editingIndex !== null) {
            $this->segments[$this->editingIndex] = $segment;
        } else {
            $this->segments[] = $segment;
        }

        $this->reindexPositions();
        $this->closeModal();
    }

    public function deleteSegment(int $index)
    {
        if (count($this->segments) <= 2) {
            session()->flash('error', 'Debe haber al menos 2 segmentos.');
            return;
        }
        
        unset($this->segments[$index]);
        $this->segments = array_values($this->segments);
        $this->reindexPositions();
    }

    public function closeModal()
    {
        $this->showSegmentModal = false;
        $this->resetSegmentForm();
    }

    protected function resetSegmentForm()
    {
        $this->seg_type = 'cash';
        $this->seg_amount = 0;
        $this->seg_probability = 10;
        $this->seg_label = '';
        $this->seg_color = '#22c55e';
        $this->editingIndex = null;
    }

    protected function reindexPositions()
    {
        foreach ($this->segments as $i => &$segment) {
            $segment['position'] = $i + 1;
        }
    }

    public function getTotalProbability(): int
    {
        return array_sum(array_column($this->segments, 'probability'));
    }

    public function render()
    {
        return view('livewire.agent.wheel-config', [
            'totalProbability' => $this->getTotalProbability(),
            'types' => [
                'cash' => 'Dinero (suma al saldo)',
                'bonus' => 'Bono',
                'free_spin' => 'Giro Extra',
                'nothing' => 'Sin Premio',
            ],
        ]);
    }
}