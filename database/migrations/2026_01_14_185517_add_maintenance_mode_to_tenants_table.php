<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->boolean('maintenance_mode')->default(false)->after('is_active');
            $table->text('maintenance_message')->nullable()->after('maintenance_mode');
            $table->boolean('maintenance_block_operations')->default(false)->after('maintenance_message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['maintenance_mode', 'maintenance_message', 'maintenance_block_operations']);
        });
    }
};
