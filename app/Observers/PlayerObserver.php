<?php

namespace App\Observers;

use App\Models\Player;
use App\Services\MessageService;
use App\Services\BonusService;

class PlayerObserver
{
    protected $messageService;
    protected $bonusService;

    public function __construct(MessageService $messageService, BonusService $bonusService)
    {
        $this->messageService = $messageService;
        $this->bonusService = $bonusService;
    }

    /**
     * Handle the Player "created" event.
     */
    public function created(Player $player): void
    {
        // Mensaje de bienvenida automático
        $this->messageService->notifyWelcome($player);
        
        // Bono de bienvenida (configurable por tenant, por ahora fijo en $500)
        $welcomeBonusAmount = 500; // TODO: hacer configurable por tenant
        
        if ($welcomeBonusAmount > 0) {
            $this->bonusService->grantWelcomeBonus($player, $welcomeBonusAmount);
        }
    }

    /**
     * Handle the Player "updated" event.
     */
    public function updated(Player $player): void
    {
        // Solo si cambió el estado
        if (!$player->wasChanged('status')) {
            return;
        }

        if ($player->status === 'suspended') {
            $reason = 'Contacta a soporte para más información';
            $this->messageService->notifyAccountSuspended($player, $reason);
        }

        if ($player->status === 'active' && $player->getOriginal('status') === 'suspended') {
            $this->messageService->notifyAccountActivated($player);
        }
    }
}