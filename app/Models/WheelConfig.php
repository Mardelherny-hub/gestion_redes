<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class WheelConfig extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'is_active',
        'daily_limit',
        'segments',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'daily_limit' => 'integer',
        'segments' => 'array',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Obtener configuración por defecto
     */
    public static function getDefaultSegments(): array
    {
        return [
            ['position' => 1, 'type' => 'cash', 'amount' => 50, 'probability' => 10, 'label' => '$50', 'color' => '#22c55e'],
            ['position' => 2, 'type' => 'cash', 'amount' => 100, 'probability' => 5, 'label' => '$100', 'color' => '#3b82f6'],
            ['position' => 3, 'type' => 'cash', 'amount' => 500, 'probability' => 2, 'label' => '$500', 'color' => '#f59e0b'],
            ['position' => 4, 'type' => 'bonus', 'amount' => 100, 'probability' => 15, 'label' => '+$100 Bono', 'color' => '#8b5cf6'],
            ['position' => 5, 'type' => 'bonus', 'amount' => 200, 'probability' => 8, 'label' => '+$200 Bono', 'color' => '#ec4899'],
            ['position' => 6, 'type' => 'free_spin', 'amount' => 0, 'probability' => 10, 'label' => 'Giro Extra', 'color' => '#06b6d4'],
            ['position' => 7, 'type' => 'nothing', 'amount' => 0, 'probability' => 25, 'label' => 'Sigue Intentando', 'color' => '#6b7280'],
            ['position' => 8, 'type' => 'nothing', 'amount' => 0, 'probability' => 25, 'label' => 'Suerte!', 'color' => '#9ca3af'],
        ];
    }
}