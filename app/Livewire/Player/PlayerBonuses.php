<?php

namespace App\Livewire\Player;

use App\Services\BonusService;
use Livewire\Component;

class PlayerBonuses extends Component
{
    protected $bonusService;

    public function boot(BonusService $bonusService)
    {
        $this->bonusService = $bonusService;
    }

    public function getBonuses()
    {
        $player = auth()->guard('player')->user();
        return $this->bonusService->getPlayerBonuses($player);
    }

    public function getTotalBonusAmount()
    {
        return $this->getBonuses()->sum('amount');
    }

    public function render()
    {
        return view('livewire.player.player-bonuses', [
            'bonuses' => $this->getBonuses(),
            'totalAmount' => $this->getTotalBonusAmount(),
        ]);
    }
}