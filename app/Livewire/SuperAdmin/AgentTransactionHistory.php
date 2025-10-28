<?php

namespace App\Livewire\SuperAdmin;

use App\Models\User;
use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithPagination;

class AgentTransactionHistory extends Component
{
    use WithPagination;

    public $agent;
    public $agentId;

    // Filtros
    public $search = '';
    public $typeFilter = 'all';
    public $statusFilter = 'all';
    public $dateFrom = '';
    public $dateTo = '';

    // Métricas
    public $totalProcessed;
    public $totalDeposits;
    public $totalWithdrawals;
    public $totalCompleted;
    public $totalRejected;

    public function mount($agent)  // ← CAMBIO AQUÍ
    {
        $this->agentId = $agent;
        $this->agent = User::findOrFail($agent);
        $this->loadMetrics();
    }

    public function loadMetrics()
    {
        $query = Transaction::where('processed_by', $this->agentId)
            ->whereNotNull('processed_at');

        $this->totalProcessed = $query->count();
        $this->totalDeposits = (clone $query)->where('type', 'deposit')->sum('amount');
        $this->totalWithdrawals = (clone $query)->where('type', 'withdrawal')->sum('amount');
        $this->totalCompleted = (clone $query)->where('status', 'completed')->count();
        $this->totalRejected = (clone $query)->where('status', 'rejected')->count();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->typeFilter = 'all';
        $this->statusFilter = 'all';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = Transaction::where('processed_by', $this->agentId)
            ->with(['player:id,name,email', 'player.tenant:id,name'])
            ->whereNotNull('processed_at');

        // Búsqueda
        if ($this->search) {
            $query->where(function($q) {
                $q->where('id', 'like', '%' . $this->search . '%')
                  ->orWhereHas('player', function($q) {
                      $q->where('name', 'ilike', '%' . $this->search . '%')
                        ->orWhere('email', 'ilike', '%' . $this->search . '%');
                  });
            });
        }

        // Filtro por tipo
        if ($this->typeFilter !== 'all') {
            $query->where('type', $this->typeFilter);
        }

        // Filtro por estado
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Filtro por fecha desde
        if ($this->dateFrom) {
            $query->whereDate('processed_at', '>=', $this->dateFrom);
        }

        // Filtro por fecha hasta
        if ($this->dateTo) {
            $query->whereDate('processed_at', '<=', $this->dateTo);
        }

        $transactions = $query->latest('processed_at')->paginate(20);

        return view('livewire.super-admin.agent-transaction-history', [
            'transactions' => $transactions,
        ])->layout('components.layouts.super-admin');
    }
}