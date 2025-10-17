<?php

namespace App\Livewire\SuperAdmin\Clients;

use App\Models\Tenant;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class Create extends Component
{
    use WithFileUploads;

    public $name = '';
    public $domain = '';
    public $database = 'gestion_redes';
    public $primary_color = '#3B82F6';
    public $secondary_color = '#10B981';
    public $logo = null;
    public $is_active = true;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:tenants,domain',
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

    public function updatedName($value)
    {
        // Auto-generar domain basado en el nombre si está vacío
        if (empty($this->domain)) {
            $this->domain = Str::slug($value);
        }
    }

    public function save()
    {
        $this->validate();

        // Procesar el logo si existe
        $logoUrl = null;
        if ($this->logo) {
            $logoUrl = $this->logo->store('logos', 'public');
            $logoUrl = asset('storage/' . $logoUrl);
        }

        // Crear el tenant
        $tenant = Tenant::create([
            'name' => $this->name,
            'domain' => $this->domain,
            'database' => $this->database,
            'primary_color' => $this->primary_color,
            'secondary_color' => $this->secondary_color,
            'logo_url' => $logoUrl,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', "Cliente {$tenant->name} creado exitosamente.");

        return $this->redirect(route('super-admin.clients.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.super-admin.clients.create')
            ->layout('components.layouts.super-admin');
    }
}