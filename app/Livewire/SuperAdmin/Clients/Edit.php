<?php

namespace App\Livewire\SuperAdmin\Clients;

use App\Models\Tenant;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public Tenant $tenant;
    public $name;
    public $domain;
    public $database;
    public $primary_color;
    public $secondary_color;
    public $logo;
    public $current_logo_url;
    public $is_active;

    public function mount(Tenant $tenant)
    {
        $this->tenant = $tenant;
        $this->name = $tenant->name;
        $this->domain = $tenant->domain;
        $this->database = $tenant->database;
        $this->primary_color = $tenant->primary_color;
        $this->secondary_color = $tenant->secondary_color;
        $this->current_logo_url = $tenant->logo_url;
        $this->is_active = $tenant->is_active;
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:tenants,domain,' . $this->tenant->id,
            'database' => 'required|string|max:255',
            'primary_color' => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
            'logo' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ];
    }

    protected $messages = [
        'name.required' => 'El nombre del cliente es obligatorio.',
        'domain.required' => 'El dominio es obligatorio.',
        'domain.unique' => 'Este dominio ya está en uso.',
        'database.required' => 'El nombre de la base de datos es obligatorio.',
        'logo.image' => 'El archivo debe ser una imagen.',
        'logo.max' => 'La imagen no puede pesar más de 2MB.',
    ];

    public function save()
    {
        $this->validate();

        // Procesar el logo si existe uno nuevo
        $logoUrl = $this->current_logo_url;
        if ($this->logo) {
            $logoUrl = $this->logo->store('logos', 'public');
            $logoUrl = asset('storage/' . $logoUrl);
        }

        // Actualizar el tenant
        $this->tenant->update([
            'name' => $this->name,
            'domain' => $this->domain,
            'database' => $this->database,
            'primary_color' => $this->primary_color,
            'secondary_color' => $this->secondary_color,
            'logo_url' => $logoUrl,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', "Cliente {$this->tenant->name} actualizado exitosamente.");

        return $this->redirect(route('super-admin.clients.index'), navigate: true);
    }

    public function removeLogo()
    {
        $this->current_logo_url = null;
    }

    public function render()
    {
        return view('livewire.super-admin.clients.edit')
            ->layout('components.layouts.super-admin');
    }
}