<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agent_api_integrations', function (Blueprint $table) {
            $table->json('extra_config')->nullable()->after('field_mappings');
        });
    }

    public function down(): void
    {
        Schema::table('agent_api_integrations', function (Blueprint $table) {
            $table->dropColumn('extra_config');
        });
    }
};