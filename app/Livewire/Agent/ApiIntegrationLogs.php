<?php

namespace App\Livewire\Agent;

use App\Models\ApiIntegrationLog;
use Livewire\Component;
use Livewire\WithPagination;
use App\Livewire\Traits\WithTenantContext;

class ApiIntegrationLogs extends Component
{
    use WithPagination, WithTenantContext;

    public string $filterAction = '';
    public string $filterStatus = '';
    public string $filterDirection = '';

    public function updatingFilterAction() { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }
    public function updatingFilterDirection() { $this->resetPage(); }

    public function mount()
    {
        if (!auth()->user()->tenant?->hasAddon('api_integration')) {
            return $this->redirect(route('dashboard'), navigate: true);
        }
    }

    public function render()
    {
        $logs = ApiIntegrationLog::query()
            ->when($this->filterAction, fn($q) => $q->where('action', $this->filterAction))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterDirection, fn($q) => $q->where('direction', $this->filterDirection))
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('livewire.agent.api-integration-logs', [
            'logs' => $logs,
        ]);
    }
}