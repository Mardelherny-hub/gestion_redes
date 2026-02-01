<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class ApiIntegrationLog extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'action',
        'direction',
        'endpoint_url',
        'request_data',
        'response_data',
        'http_status',
        'status',
        'error_message',
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
    ];
}