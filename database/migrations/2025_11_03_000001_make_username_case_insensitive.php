<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Normalizar usernames existentes a minúsculas
        DB::statement("UPDATE players SET username = LOWER(username)");
        
        // 2. Eliminar índice único anterior
        Schema::table('players', function (Blueprint $table) {
            $table->dropUnique('players_tenant_username_unique');
        });
        
        // 3. Crear índice único case-insensitive usando expresión
        DB::statement('CREATE UNIQUE INDEX players_tenant_username_unique ON players (tenant_id, LOWER(username))');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX players_tenant_username_unique');
        
        Schema::table('players', function (Blueprint $table) {
            $table->unique(['tenant_id', 'username'], 'players_tenant_username_unique');
        });
    }
};