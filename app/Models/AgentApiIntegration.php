<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class AgentApiIntegration extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'enabled',
        'base_url',
        'auth_type',
        'auth_credentials',
        'endpoints',
        'field_mappings',
        'webhook_secret',
        'extra_config',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'endpoints' => 'array',
        'field_mappings' => 'array',
        'auth_credentials' => 'encrypted',
        'extra_config' => 'array',
    ];
}