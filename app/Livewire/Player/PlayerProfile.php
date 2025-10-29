<?php

namespace App\Livewire\Player;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;

class PlayerProfile extends Component
{
    public $name;
    public $username; 
    public $email;
    public $phone;
    public $current_password = '';
    public $new_password = '';
    public $new_password_confirmation = '';

    public function mount()
    {
        $player = auth()->guard('player')->user();
        $this->name = $player->name;
        $this->username = $player->username;
        $this->email = $player->email;
        $this->phone = $player->phone;
    }

    public function updateProfile()
    {
        $this->validate([
            'name' => 'required|min:3|max:255',
            'username' => [  // AGREGADO
                'required',
                'min:10',
                'max:15',
                'regex:/^[a-zA-Z][a-zA-Z0-9]*$/',
                Rule::unique('players', 'username')
                    ->where('tenant_id', $player->tenant_id)
                    ->ignore($player->id)
            ],
            'email' => 'required|email|max:255|unique:players,email,' . auth()->guard('player')->id(),
            'phone' => 'required|min:10|max:20',
        ], [
            'name.required' => 'El nombre es obligatorio',
            'name.min' => 'El nombre debe tener al menos 3 caracteres',
            'username.required' => 'El nombre de usuario es obligatorio',
            'username.min' => 'El nombre de usuario debe tener al menos 10 caracteres',
            'username.max' => 'El nombre de usuario no puede tener más de 15 caracteres',
            'username.regex' => 'El nombre de usuario solo puede contener letras y números',
            'username.unique' => 'Este nombre de usuario ya está en uso',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe ser válido',
            'email.unique' => 'Este email ya está en uso',
            'phone.required' => 'El teléfono es obligatorio',
            'phone.min' => 'El teléfono debe tener al menos 10 caracteres',
        ]);

        $player = auth()->guard('player')->user();
        
        $player->update([
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone,
        ]);

        session()->flash('success', 'Perfil actualizado correctamente');
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'La contraseña actual es obligatoria',
            'new_password.required' => 'La nueva contraseña es obligatoria',
            'new_password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'new_password.confirmed' => 'Las contraseñas no coinciden',
        ]);

        $player = auth()->guard('player')->user();

        if (!Hash::check($this->current_password, $player->password)) {
            $this->addError('current_password', 'La contraseña actual es incorrecta');
            return;
        }

        $player->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        
        session()->flash('password_success', 'Contraseña actualizada correctamente');
    }

    public function render()
    {
        $player = auth()->guard('player')->user();
        
        return view('livewire.player.player-profile', [
            'player' => $player,
        ]);
    }
}