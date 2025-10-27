<?php

namespace App\Livewire\Traits;

trait ChecksPlayerStatus
{
    public function checkPlayerStatus()
    {
        $player = auth('player')->user();
        
        if (!$player) {
            return redirect()->route('player.login');
        }
        
        if ($player->isBlocked()) {
            session()->flash('error', 'Tu cuenta ha sido bloqueada. Por favor, contacta con soporte.');
            return redirect()->route('player.blocked');
        }
        
        if ($player->isSuspended()) {
            session()->flash('warning', 'Tu cuenta está suspendida temporalmente. Contacta con soporte para más información.');
            return redirect()->route('player.suspended');
        }
    }
    
    public function bootChecksPlayerStatus()
    {
        // Se ejecuta automáticamente al montar el componente
        if (method_exists($this, 'mount')) {
            $originalMount = $this->mount(...);
            $this->mount = function(...$args) use ($originalMount) {
                $this->checkPlayerStatus();
                return $originalMount(...$args);
            };
        }
    }
}