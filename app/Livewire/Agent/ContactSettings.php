<?php

namespace App\Livewire\Agent;

use Livewire\Component;
use App\Livewire\Traits\WithTenantContext;

class ContactSettings extends Component
{
    use WithTenantContext;
    
    public $showModal = false;
    
    public $whatsapp_number = '';
    public $casino_url = '';

    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        $tenant = auth()->user()->tenant;
        
        if ($tenant) {
            $this->whatsapp_number = $tenant->whatsapp_number ?? '';
            $this->casino_url = $tenant->casino_url ?? '';
        }
    }

    public function openModal()
    {
        $this->loadSettings();
        $this->showModal = true;
        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function save()
    {
        // Persistir WhatsApp en el formato que requiere wa.me: solo dígitos.
        // Se toleran signos habituales de presentación al ingresarlo.
        $this->whatsapp_number = preg_replace('/[\s()+.\-]+/', '', trim($this->whatsapp_number));

        $this->validate([
            'whatsapp_number' => ['nullable', 'string', 'regex:/^\d{7,15}$/'],
            'casino_url' => 'nullable|url|max:255',
        ], [
            'whatsapp_number.regex' => 'Ingresá el número en formato internacional, por ejemplo: +54 9 11 5404-9260.',
            'casino_url.url' => 'La URL del casino debe ser válida',
            'casino_url.max' => 'La URL no puede exceder 255 caracteres',
        ]);

        $tenant = auth()->user()->tenant;
        
        $tenant->update([
            'whatsapp_number' => $this->whatsapp_number,
            'casino_url' => $this->casino_url,
        ]);

        // Activity log
        activity()
            ->performedOn($tenant)
            ->causedBy(auth()->user())
            ->withProperties([
                'whatsapp_number' => $this->whatsapp_number,
                'casino_url' => $this->casino_url,
            ])
            ->log('Configuración de contacto actualizada');

        session()->flash('message', 'Configuración guardada correctamente');
        
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.agent.contact-settings');
    }
}
