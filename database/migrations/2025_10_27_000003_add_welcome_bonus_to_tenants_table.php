<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->boolean('welcome_bonus_enabled')->default(false)->after('is_active');
            $table->decimal('welcome_bonus_amount', 10, 2)->default(0)->after('welcome_bonus_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['welcome_bonus_enabled', 'welcome_bonus_amount']);
        });
    }
};