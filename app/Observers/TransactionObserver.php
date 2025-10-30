<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Models\Player;
use App\Services\MessageService;
use App\Services\BonusService;

class TransactionObserver
{
    protected $messageService;
    protected $bonusService;

    public function __construct(MessageService $messageService, BonusService $bonusService)
    {
        $this->messageService = $messageService;
        $this->bonusService = $bonusService;
    }

    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        // Solo notificar si es una solicitud del jugador (deposit o withdrawal)
        if ($transaction->type === 'deposit' && $transaction->status === 'pending') {
            $this->messageService->notifyDepositRequest($transaction);
        }

        if ($transaction->type === 'withdrawal' && $transaction->status === 'pending') {
            $this->messageService->notifyWithdrawalRequest($transaction);
        }
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        // Solo si cambió el estado
        if (!$transaction->wasChanged('status')) {
            return;
        }

        // Depósitos
        if ($transaction->type === 'deposit') {
            if ($transaction->status === 'completed') {
                $this->messageService->notifyDepositApproved($transaction);
                
                // Verificar si es primer depósito para bono de referido
                $this->checkReferralBonus($transaction);
            }

            if ($transaction->status === 'rejected') {
                $reason = $transaction->notes ?? 'No se especificó un motivo';
                $this->messageService->notifyDepositRejected($transaction, $reason);
            }
        }

        // Retiros
        if ($transaction->type === 'withdrawal') {
            if ($transaction->status === 'completed') {
                $this->messageService->notifyWithdrawalApproved($transaction);
            }

            if ($transaction->status === 'rejected') {
                $reason = $transaction->notes ?? 'No se especificó un motivo';
                $this->messageService->notifyWithdrawalRejected($transaction, $reason);
            }
        }
    }

    /**
     * Verificar y otorgar bono por referido en primer depósito
     */
    protected function checkReferralBonus(Transaction $transaction): void
    {
        $player = $transaction->player;
        
        // Verificar si es el primer depósito completado
        $isFirstDeposit = Transaction::where('player_id', $player->id)
            ->where('type', 'deposit')
            ->where('status', 'completed')
            ->count() === 1;
        
        if (!$isFirstDeposit || !$player->referred_by) {
            return;
        }
        
        // Obtener el referidor
        $referrer = Player::find($player->referred_by);
        
        if (!$referrer || !$referrer->isActive()) {
            return;
        }
        
        // Configuración del bono (TODO: hacer configurable por tenant)
        $referralBonusAmount = 200; // $200 para cada uno
        
        // Otorgar bono al referidor
        $this->bonusService->grantReferralBonus(
            $referrer,
            $referralBonusAmount,
            $player->name
        );
        
        // Otorgar bono al referido
        $this->bonusService->grantReferralBonus(
            $player,
            $referralBonusAmount,
            "tu primer depósito"
        );
    }
}