<?php

namespace App\Livewire\Agent;

use App\Models\Player;
use App\Services\MessageService;
use Livewire\Component;

class BroadcastMessage extends Component
{
    public $showModal = false;
    public $message = '';
    public $activePlayersCount = 0;

    protected $messageService;

    public function boot(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    public function openModal()
    {
        $this->activePlayersCount = Player::where('tenant_id', auth()->user()->tenant_id)
            ->where('status', 'active')
            ->count();
        
        $this->message = '';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->message = '';
    }

    public function send()
    {
        $this->validate([
            'message' => 'required|min:5|max:1000',
        ], [
            'message.required' => 'El mensaje es obligatorio',
            'message.min' => 'El mensaje debe tener al menos 5 caracteres',
            'message.max' => 'El mensaje no puede superar los 1000 caracteres',
        ]);

        $count = $this->messageService->broadcastMessage(
            auth()->user()->tenant_id,
            auth()->user(),
            $this->message
        );

        $this->closeModal();
        
        session()->flash('success', "Mensaje enviado a {$count} jugadores");
        
        $this->dispatch('broadcastSent');
    }

    public function render()
    {
        return view('livewire.agent.broadcast-message');
    }
}
