<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->boolean('referral_bonus_enabled')->default(false)->after('welcome_bonus_amount');
            $table->decimal('referral_bonus_amount', 10, 2)->default(0)->after('referral_bonus_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['referral_bonus_enabled', 'referral_bonus_amount']);
        });
    }
};