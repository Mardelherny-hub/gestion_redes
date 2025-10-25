<?php

namespace App\Livewire\Player;

use Livewire\Component;
use Livewire\WithPagination;

class Transactions extends Component
{
    use WithPagination;

    public $player;
    public $typeFilter = 'all';
    public $statusFilter = 'all';
    public $search = '';

    public function mount()
    {
        $this->player = auth()->guard('player')->user();
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

    public function getTransactionsProperty()
    {
        $query = $this->player->transactions()->latest();

        // Filtro por tipo
        if ($this->typeFilter !== 'all') {
            $query->where('type', $this->typeFilter);
        }

        // Filtro por estado
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Búsqueda por monto o hash
        if ($this->search) {
            $query->where(function($q) {
                $q->where('amount', 'like', '%' . $this->search . '%')
                  ->orWhere('transaction_hash', 'like', '%' . $this->search . '%');
            });
        }

        return $query->paginate(20);
    }

    public function render()
    {
        return view('livewire.player.transactions', [
            'transactions' => $this->transactions,
        ])->layout('components.layouts.player');
    }
}