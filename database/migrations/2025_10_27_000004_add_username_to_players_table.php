<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('players', function (Blueprint $table) {
            // Agregar campo username nullable inicialmente
            $table->string('username', 15)->nullable()->after('name');
        });
        
        // Poblar usernames existentes con valores temporales
        DB::statement("
            UPDATE players 
            SET username = CONCAT('user', id)
            WHERE username IS NULL
        ");
        
        // Ahora hacer el campo NOT NULL y agregar índice único
        Schema::table('players', function (Blueprint $table) {
            $table->string('username', 15)->nullable(false)->change();
            $table->unique(['tenant_id', 'username'], 'players_tenant_username_unique');
        });
    }

    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropUnique('players_tenant_username_unique');
            $table->dropColumn('username');
        });
    }
};