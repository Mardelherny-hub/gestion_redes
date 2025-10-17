<?php

namespace App\Livewire\SuperAdmin;

use App\Models\Tenant;
use App\Models\Player;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public $totalClients;
    public $activeClients;
    public $totalPlayers;
    public $totalBalance;
    public $totalTransactionsToday;
    public $totalDepositsToday;
    public $totalWithdrawalsToday;
    public $pendingTransactions;
    public $recentClients;
    public $topClients;

    public function mount()
    {
        $this->loadMetrics();
    }

    public function loadMetrics()
    {
        // Total de clientes
        $this->totalClients = Tenant::count();

        // Clientes activos
        $this->activeClients = Tenant::where('is_active', true)->count();

        // Total de jugadores (todos los clientes)
        $this->totalPlayers = Player::count();

        // Saldo total en el sistema (todos los clientes)
        $this->totalBalance = Player::sum('balance');

        // Transacciones de hoy
        $this->totalTransactionsToday = Transaction::whereDate('created_at', today())->count();

        // Depósitos de hoy
        $this->totalDepositsToday = Transaction::whereDate('created_at', today())
            ->where('type', 'deposit')
            ->where('status', 'completed')
            ->sum('amount');

        // Retiros de hoy
        $this->totalWithdrawalsToday = Transaction::whereDate('created_at', today())
            ->where('type', 'withdrawal')
            ->where('status', 'completed')
            ->sum('amount');

        // Transacciones pendientes (todos los clientes)
        $this->pendingTransactions = Transaction::where('status', 'pending')->count();

        // Últimos 5 clientes creados
        $this->recentClients = Tenant::latest()->limit(5)->get();

        // Top 5 clientes por saldo total
        $this->topClients = Tenant::withSum('players', 'balance')
            ->orderByDesc('players_sum_balance')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.super-admin.dashboard')
            ->layout('components.layouts.super-admin');
    }
}