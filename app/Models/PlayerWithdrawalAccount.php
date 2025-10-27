<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlayerWithdrawalAccount extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'player_id',
        'account_type',
        'account_number',
        'alias',
        'holder_name',
        'holder_dni',
        'bank_name',
        'is_default',
        'is_verified',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_verified' => 'boolean',
    ];

    // Relaciones
    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    // Métodos
    public function setAsDefault()
    {
        // Remover default de otras cuentas
        self::where('player_id', $this->player_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);
        
        $this->update(['is_default' => true]);
        
        // Activity log
        activity()
            ->performedOn($this)
            ->causedBy($this->player)
            ->log('Cuenta de retiro establecida como predeterminada');
    }

    public function verify()
    {
        $this->update(['is_verified' => true]);
        
        // Activity log
        activity()
            ->performedOn($this)
            ->causedBy(auth()->user())
            ->log('Cuenta de retiro verificada');
    }

    // Scopes
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // Accessors
    public function getFormattedAccountAttribute()
    {
        if ($this->account_type === 'alias') {
            return $this->alias;
        }
        
        // Formatear CBU/CVU
        return substr($this->account_number, 0, 4) . '...' . substr($this->account_number, -4);
    }

    public function getDisplayNameAttribute()
    {
        $type = strtoupper($this->account_type);
        return "{$type} - {$this->holder_name} ({$this->formatted_account})";
    }
}