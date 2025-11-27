<?php

namespace App\Livewire\Player;

use App\Services\WheelService;
use Livewire\Component;

class SpinWheel extends Component
{
    public $isSpinning = false;
    public $hasSpunToday = false;
    public $lastPrize = null;
    public $showResult = false;

    protected $wheelService;

    public function boot(WheelService $wheelService)
    {
        $this->wheelService = $wheelService;
    }

    public function mount()
    {
        $player = auth()->guard('player')->user();
        $this->hasSpunToday = !$this->wheelService->canSpinToday($player);
        $this->isSpinning = false; // ← AGREGAR ESTA LÍNEA
        $this->showResult = false; // ← AGREGAR ESTA LÍNEA

         
    // DEBUG - Eliminar después
    logger()->info('SpinWheel Mount', [
        'isSpinning' => $this->isSpinning,
        'hasSpunToday' => $this->hasSpunToday
    ]);
    }

    public function spin()
    {
        // Verificar SOLO isSpinning (no hasSpunToday, eso lo maneja el service)
        if ($this->isSpinning) {
            return;
        }

        try {
            $player = auth()->guard('player')->user();
            
            $this->isSpinning = true;
            
            $result = $this->wheelService->spin($player);
            
            $this->lastPrize = $result['prize'];
            
            // NO actualizar hasSpunToday aquí, hacerlo en showPrizeResult
            
            // Esperar a que termine la animación
            $this->dispatch('spin-complete', prize: $this->lastPrize);
            
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            $this->isSpinning = false;
        }
    }

    public function showPrizeResult()
    {
        $this->showResult = true;
        $this->isSpinning = false;
        $this->hasSpunToday = true; // ← MOVER AQUÍ
    }


    public function closeResult()
    {
        $this->showResult = false;
    }

    public function getPrizes()
    {
        $player = auth()->guard('player')->user();
        return $this->wheelService->getPrizeConfiguration($player->tenant_id);
    }

    public function getSpinHistory()
    {
        $player = auth()->guard('player')->user();
        return $this->wheelService->getPlayerSpins($player, 10);
    }

    public function render()
    {
        return view('livewire.player.spin-wheel', [
            'prizes' => $this->getPrizes(),
            'history' => $this->getSpinHistory(),
        ]);
    }
}