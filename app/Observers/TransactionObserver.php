<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Models\Player;
use App\Services\MessageService;
use App\Services\BonusService;
use App\Services\WebPushService;

class TransactionObserver
{
    protected $messageService;
    protected $bonusService;
    protected $webPushService;

    public function __construct(MessageService $messageService, BonusService $bonusService)
    {
        $this->messageService = $messageService;
        $this->bonusService = $bonusService;
        $this->webPushService = new WebPushService();
    }

    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        // Notificar solo si es pending
        if ($transaction->status !== 'pending') {
            return;
        }

        // Notificar según el tipo
        if ($transaction->type === 'deposit') {
            $this->messageService->notifyDepositRequest($transaction);
        }

        if ($transaction->type === 'withdrawal') {
            $this->messageService->notifyWithdrawalRequest($transaction);
        }

        // Push notification a agentes del tenant (depósitos y retiros)
        if (in_array($transaction->type, ['deposit', 'withdrawal'])) {
            $this->webPushService->sendToTenantUsers(
                $transaction->player->tenant,
                '💰 Nueva transacción pendiente',
                ucfirst($transaction->type === 'deposit' ? 'Depósito' : 'Retiro') . ' de $' . number_format($transaction->amount, 2) . ' - ' . $transaction->player->name,
                '/dashboard/transactions/pending'
            );
        }

        // Notificaciones para solicitudes de cuenta
        if ($transaction->type === 'account_creation') {
            $this->messageService->sendSystemMessage(
                $transaction->player,
                '📝 Tu solicitud de creación de usuario fue recibida. Te avisaremos cuando sea procesada.',
                'account',
                $transaction
            );
        }

        if ($transaction->type === 'account_unlock') {
            $this->messageService->sendSystemMessage(
                $transaction->player,
                '🔓 Tu solicitud de desbloqueo fue recibida. Te avisaremos cuando sea procesada.',
                'account',
                $transaction
            );
        }

        if ($transaction->type === 'password_reset') {
            $this->messageService->sendSystemMessage(
                $transaction->player,
                '🔑 Tu solicitud de cambio de contraseña fue recibida. Te avisaremos cuando sea procesada.',
                'account',
                $transaction
            );
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
                
                // Push al player
                $this->webPushService->sendToPlayer(
                    $transaction->player,
                    '✅ Depósito aprobado',
                    'Tu depósito de $' . number_format($transaction->amount, 2) . ' fue acreditado',
                    '/player/transactions'
                );
                
                // Verificar si es primer depósito para bonos
                $this->checkFirstDepositBonuses($transaction);
            }

            if ($transaction->status === 'rejected') {
                // Extraer el motivo del campo notes
                $reason = $transaction->notes ?? 'No se especificó un motivo';
                
                // Si notes contiene el formato "RECHAZADO: motivo", extraer solo el motivo
                if (str_contains($reason, 'RECHAZADO: ')) {
                    $reason = str_replace('RECHAZADO: ', '', $reason);
                    // Si tiene el formato antiguo con pipes, tomar la última parte
                    if (str_contains($reason, ' | ')) {
                        $parts = explode(' | ', $reason);
                        $reason = end($parts);
                        if (str_starts_with($reason, 'RECHAZADO: ')) {
                            $reason = str_replace('RECHAZADO: ', '', $reason);
                        }
                    }
                }
                
                $typeLabel = $transaction->type === 'deposit' ? 'depósito' : 
                            ($transaction->type === 'withdrawal' ? 'retiro' : 
                            ($transaction->type === 'account_creation' ? 'creación de usuario' :
                            ($transaction->type === 'account_unlock' ? 'desbloqueo' :
                            ($transaction->type === 'password_reset' ? 'cambio de contraseña' : 'solicitud'))));
                
                if ($transaction->type === 'deposit') {
                    $this->messageService->notifyDepositRejected($transaction, $reason);
                    
                    // Push al player
                    $this->webPushService->sendToPlayer(
                        $transaction->player,
                        '❌ Depósito rechazado',
                        'Tu depósito de $' . number_format($transaction->amount, 2) . ' fue rechazado',
                        '/player/transactions'
                    );
                } elseif ($transaction->type === 'withdrawal') {
                    $this->messageService->notifyWithdrawalRejected($transaction, $reason);
                }
                // Para los tipos de cuenta, ya se maneja en TransactionApproval/Rejection
            }
        }

        // Retiros
        if ($transaction->type === 'withdrawal') {
            if ($transaction->status === 'completed') {
                $this->messageService->notifyWithdrawalApproved($transaction);
                
                // Push al player
                $this->webPushService->sendToPlayer(
                    $transaction->player,
                    '✅ Retiro aprobado',
                    'Tu retiro de $' . number_format($transaction->amount, 2) . ' fue procesado',
                    '/player/transactions'
                );
            }

            if ($transaction->status === 'rejected') {
                // Extraer el motivo del campo notes
                $reason = $transaction->notes ?? 'No se especificó un motivo';
                
                // Si notes contiene el formato "RECHAZADO: motivo", extraer solo el motivo
                if (str_contains($reason, 'RECHAZADO: ')) {
                    $reason = str_replace('RECHAZADO: ', '', $reason);
                    // Si tiene el formato antiguo con pipes, tomar la última parte
                    if (str_contains($reason, ' | ')) {
                        $parts = explode(' | ', $reason);
                        $reason = end($parts);
                        if (str_starts_with($reason, 'RECHAZADO: ')) {
                            $reason = str_replace('RECHAZADO: ', '', $reason);
                        }
                    }
                }
                
                $typeLabel = $transaction->type === 'deposit' ? 'depósito' : 
                            ($transaction->type === 'withdrawal' ? 'retiro' : 
                            ($transaction->type === 'account_creation' ? 'creación de usuario' :
                            ($transaction->type === 'account_unlock' ? 'desbloqueo' :
                            ($transaction->type === 'password_reset' ? 'cambio de contraseña' : 'solicitud'))));
                
                if ($transaction->type === 'deposit') {
                    $this->messageService->notifyDepositRejected($transaction, $reason);
                } elseif ($transaction->type === 'withdrawal') {
                    $this->messageService->notifyWithdrawalRejected($transaction, $reason);
                    
                    // Push al player
                    $this->webPushService->sendToPlayer(
                        $transaction->player,
                        '❌ Retiro rechazado',
                        'Tu retiro de $' . number_format($transaction->amount, 2) . ' fue rechazado',
                        '/player/transactions'
                    );
                }
                // Para los tipos de cuenta, ya se maneja en TransactionApproval/Rejection
            }
        }

        // Solicitudes de cuenta (account_creation, account_unlock, password_reset)
        if ($transaction->isAccountRequest()) {
            if ($transaction->status === 'completed') {
                // Extraer credenciales del campo notes
                $notes = $transaction->notes ?? '';
                
                if ($transaction->type === 'account_creation') {
                    // Buscar patrón: "Usuario: xxxx | Contraseña: yyyy"
                    if (preg_match('/Usuario:\s*(\S+)\s*\|\s*Contraseña:\s*(\S+)/', $notes, $matches)) {
                        $username = $matches[1];
                        $password = $matches[2];
                        $this->messageService->notifyAccountCreated($transaction, $username, $password);
                    }
                }
                
                if ($transaction->type === 'account_unlock') {
                    $this->messageService->notifyAccountUnlocked($transaction);
                }
                
                if ($transaction->type === 'password_reset') {
                    // Buscar patrón: "Nueva contraseña: xxxx"
                    if (preg_match('/Nueva contraseña:\s*(\S+)/', $notes, $matches)) {
                        $newPassword = $matches[1];
                        $this->messageService->notifyPasswordChanged($transaction, $newPassword);
                    }
                }
            }
        }
    }

    /**
     * Verificar y otorgar bonos en primer depósito (bienvenida + referido)
     */
    protected function checkFirstDepositBonuses(Transaction $transaction): void
    {
        $player = $transaction->player;
        $tenant = $player->tenant;
        
        // Verificar si es el primer depósito completado
        $isFirstDeposit = Transaction::where('player_id', $player->id)
            ->where('type', 'deposit')
            ->where('status', 'completed')
            ->count() === 1;
        
        if (!$isFirstDeposit) {
            return;
        }
        
        // 1. Bono de bienvenida por porcentaje (si está habilitado)
        if ($tenant->welcome_bonus_enabled && $tenant->welcome_bonus_is_percentage && $tenant->welcome_bonus_amount > 0) {
            $this->grantWelcomeBonusPercentage($player, $transaction, $tenant);
        }
        
        // 2. Bono por referido
        $this->checkReferralBonus($player, $tenant);
    }

    /**
     * Otorgar bono de bienvenida por porcentaje del primer depósito
     */
    protected function grantWelcomeBonusPercentage(Player $player, Transaction $transaction, $tenant): void
    {
        // Calcular el bono como porcentaje del depósito
        $percentage = $tenant->welcome_bonus_amount; // Ej: 20 = 20%
        $bonusAmount = ($transaction->amount * $percentage) / 100;
        
        // Aplicar tope máximo si existe
        if ($tenant->welcome_bonus_max && $bonusAmount > $tenant->welcome_bonus_max) {
            $bonusAmount = $tenant->welcome_bonus_max;
        }
        
        // Otorgar el bono
        $this->bonusService->grantWelcomeBonus($player, $bonusAmount);
        
        activity()
            ->performedOn($player)
            ->withProperties([
                'deposit_amount' => $transaction->amount,
                'percentage' => $percentage,
                'bonus_amount' => $bonusAmount,
            ])
            ->log('Bono de bienvenida por porcentaje otorgado');
    }

    /**
     * Verificar y otorgar bono por referido
     */
    protected function checkReferralBonus(Player $player, $tenant): void
    {
        if (!$player->referred_by) {
            return;
        }
        
        // Obtener el referidor
        $referrer = Player::find($player->referred_by);
        
        if (!$referrer || !$referrer->isActive()) {
            return;
        }
        
        // Verificar si el bono de referido está habilitado
        if (!$tenant->referral_bonus_enabled || $tenant->referral_bonus_amount <= 0) {
            return;
        }
        
        $referralBonusAmount = $tenant->referral_bonus_amount;
        $target = $tenant->referral_bonus_target ?? 'both';
        
        // Otorgar bono al referidor
        if (in_array($target, ['referrer', 'both'])) {
            $this->bonusService->grantReferralBonus(
                $referrer,
                $referralBonusAmount,
                $player->display_name
            );
        }
        
        // Otorgar bono al referido
        if (in_array($target, ['referred', 'both'])) {
            $this->bonusService->grantReferralBonus(
                $player,
                $referralBonusAmount,
                "tu primer depósito"
            );
        }
    }
}