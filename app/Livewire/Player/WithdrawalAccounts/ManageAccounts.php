<?php

namespace App\Livewire\Player\WithdrawalAccounts;

use App\Models\PlayerWithdrawalAccount;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Livewire\Traits\WithToast;

class ManageAccounts extends Component
{
    use WithToast;

    public $showModal = false;
    public $editingId = null;
    
    // Campos del formulario
    public $account_type = 'cbu';
    public $account_number = '';
    public $alias = '';
    public $holder_name = '';
    public $holder_dni = '';
    public $bank_name = '';
    public $is_default = false;

    protected function rules()
    {
        $rules = [
            'account_type' => 'required|in:cbu,cvu,alias',
            'holder_name' => 'required|min:3',
            'holder_dni' => 'nullable|numeric|digits:8',
            'bank_name' => 'nullable|string',
            'is_default' => 'boolean',
        ];

        if ($this->account_type === 'alias') {
            $rules['alias'] = 'required|string|min:6|max:20';
        } else {
            $rules['account_number'] = 'required|numeric|digits:22';
        }

        return $rules;
    }

    protected $messages = [
        'account_type.required' => 'Selecciona el tipo de cuenta',
        'account_number.required' => 'El número de cuenta es obligatorio',
        'account_number.numeric' => 'Solo números',
        'account_number.digits' => 'El CBU/CVU debe tener 22 dígitos',
        'alias.required' => 'El alias es obligatorio',
        'alias.min' => 'El alias debe tener al menos 6 caracteres',
        'holder_name.required' => 'El nombre del titular es obligatorio',
        'holder_dni.digits' => 'El DNI debe tener 8 dígitos',
    ];

    public function openModal($accountId = null)
    {
        $this->editingId = $accountId;
        
        if ($accountId) {
            $account = PlayerWithdrawalAccount::findOrFail($accountId);
            $this->account_type = $account->account_type;
            $this->account_number = $account->account_number;
            $this->alias = $account->alias;
            $this->holder_name = $account->holder_name;
            $this->holder_dni = $account->holder_dni;
            $this->bank_name = $account->bank_name;
            $this->is_default = $account->is_default;
        } else {
            $player = auth('player')->user();
            $this->holder_name = $player->name;
            $this->is_default = !$player->withdrawalAccounts()->exists();
        }
        
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $player = auth('player')->user();

        $data = [
            'tenant_id' => $player->tenant_id,
            'player_id' => $player->id,
            'account_type' => $this->account_type,
            'account_number' => $this->account_number,
            'alias' => $this->alias,
            'holder_name' => $this->holder_name,
            'holder_dni' => $this->holder_dni,
            'bank_name' => $this->bank_name,
            'is_default' => $this->is_default,
        ];

        if ($this->editingId) {
            $account = PlayerWithdrawalAccount::findOrFail($this->editingId);
            $account->update($data);
            $message = 'Cuenta actualizada correctamente';
        } else {
            $account = PlayerWithdrawalAccount::create($data);
            $message = 'Cuenta agregada correctamente';
        }

        // Si marcó como predeterminada
        if ($this->is_default) {
            $account->setAsDefault();
        }

        // Activity log
        activity()
            ->performedOn($account)
            ->causedBy($player)
            ->log($this->editingId ? 'Cuenta de retiro actualizada' : 'Nueva cuenta de retiro agregada');

        $this->showToast($message, 'success');
        $this->closeModal();
        $this->dispatch('accountSaved');
    }

    public function setAsDefault($accountId)
    {
        $account = PlayerWithdrawalAccount::findOrFail($accountId);
        $account->setAsDefault();
        
        $this->showToast('Cuenta predeterminada actualizada', 'success');
        $this->dispatch('accountSaved');
    }

    public function delete($accountId)
    {
        $account = PlayerWithdrawalAccount::findOrFail($accountId);
        $account->delete();

        // Activity log
        activity()
            ->performedOn($account)
            ->causedBy(auth('player')->user())
            ->log('Cuenta de retiro eliminada');

        $this->showToast('Cuenta eliminada correctamente', 'success');
        $this->dispatch('accountSaved');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset([
            'editingId',
            'account_type',
            'account_number',
            'alias',
            'holder_name',
            'holder_dni',
            'bank_name',
            'is_default'
        ]);
        $this->resetValidation();
    }

    #[On('accountSaved')]
    public function refresh()
    {
        // Refresh del componente
    }

    public function render()
    {
        $player = auth('player')->user();
        $accounts = $player->withdrawalAccounts()->latest()->get();

        return view('livewire.player.withdrawal-accounts.manage-accounts', [
            'accounts' => $accounts
        ]);
    }
}