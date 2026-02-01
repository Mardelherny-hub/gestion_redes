<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE agent_api_integrations DROP CONSTRAINT agent_api_integrations_auth_type_check");
        DB::statement("ALTER TABLE agent_api_integrations ADD CONSTRAINT agent_api_integrations_auth_type_check CHECK (auth_type::text = ANY (ARRAY['api_key'::text, 'bearer'::text, 'basic'::text, 'token_body'::text]))");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE agent_api_integrations DROP CONSTRAINT agent_api_integrations_auth_type_check");
        DB::statement("ALTER TABLE agent_api_integrations ADD CONSTRAINT agent_api_integrations_auth_type_check CHECK (auth_type::text = ANY (ARRAY['api_key'::text, 'bearer'::text, 'basic'::text]))");
    }
};