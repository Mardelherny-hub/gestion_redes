<?php

namespace App\Livewire\Agent\Transactions;

use App\Models\Transaction;
use Livewire\Component;
use Carbon\Carbon;
use App\Livewire\Traits\WithTenantContext;

class TransactionMonitor extends Component
{
    use WithTenantContext;
    
    public $pendingCount = 0;
    public $lastUpdate;
    
    // Para refrescar automáticamente
    protected $listeners = [
        'refreshMonitor' => '$refresh',
        'transactionProcessed' => '$refresh'
    ];

    public function mount()
    {
        $this->updateData();
    }

    public function updateData()
    {
        $this->pendingCount = Transaction::pending()->count();
        $this->lastUpdate = now();
    }

    public function refresh()
    {
        $this->updateData();
        $this->dispatch('monitorRefreshed', count: $this->pendingCount);
    }

    public function getUrgencyClass($createdAt)
    {
        $hoursAgo = Carbon::parse($createdAt)->diffInHours(now());
        
        if ($hoursAgo > 24) {
            return 'border-red-500 bg-red-50 dark:bg-red-900/20'; // Urgente
        } elseif ($hoursAgo >= 6) {
            return 'border-yellow-500 bg-yellow-50 dark:bg-yellow-900/20'; // Advertencia
        }
        
        return 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800'; // Normal
    }

    public function getUrgencyBadge($createdAt)
    {
        $hoursAgo = Carbon::parse($createdAt)->diffInHours(now());
        
        if ($hoursAgo > 24) {
            return [
                'class' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                'text' => 'URGENTE'
            ];
        } elseif ($hoursAgo >= 6) {
            return [
                'class' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                'text' => 'PRIORIDAD'
            ];
        }
        
        return [
            'class' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'text' => 'NORMAL'
        ];
    }

    public function getTimeWaiting($createdAt)
    {
        return Carbon::parse($createdAt)->diffForHumans();
    }

    public function render()
    {
        $transactions = Transaction::with(['player:id,name,balance'])
            ->pending()
            ->oldest() // Más antiguas primero
            ->get();

        return view('livewire.agent.transactions.transaction-monitor', [
            'transactions' => $transactions,
        ]);
    }
}