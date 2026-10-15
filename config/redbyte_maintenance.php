<?php

return [
    // This endpoint never becomes public simply because a feature was deployed.
    'enabled' => env('REDBYTE_MAINTENANCE_API_ENABLED', false),
    'read_token' => env('REDBYTE_MAINTENANCE_API_TOKEN', ''),
    'client' => 'nextlevel',
    'timezone' => 'America/Argentina/Buenos_Aires',
];
