<?php

namespace App\Livewire\Traits;

trait WithTenantContext
{
    public function bootWithTenantContext()
    {
        if (!app()->has('tenant') && session('current_tenant_id')) {
            $tenant = \App\Models\Tenant::find(session('current_tenant_id'));
            if ($tenant) {
                app()->instance('tenant', $tenant);
            }
        }
    }
}