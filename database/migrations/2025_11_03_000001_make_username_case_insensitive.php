<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Identificar y resolver duplicados antes de normalizar
        DB::statement("
            UPDATE players p1
            SET username = username || '_' || id
            WHERE EXISTS (
                SELECT 1 FROM players p2
                WHERE p2.tenant_id = p1.tenant_id
                AND LOWER(p2.username) = LOWER(p1.username)
                AND p2.id < p1.id
            )
        ");
        
        // 2. Ahora normalizar a minúsculas (ya sin duplicados)
        DB::statement("UPDATE players SET username = LOWER(username)");
        
        // 3. Eliminar índice único anterior
        Schema::table('players', function (Blueprint $table) {
            $table->dropUnique('players_tenant_username_unique');
        });
        
        // 4. Crear índice único case-insensitive
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