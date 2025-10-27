<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_withdrawal_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            
            // Tipo de cuenta
            $table->enum('account_type', ['cbu', 'cvu', 'alias'])->default('cbu');
            
            // Datos de la cuenta
            $table->string('account_number'); // CBU/CVU
            $table->string('alias')->nullable(); // Alias de Mercado Pago, etc.
            $table->string('holder_name'); // Titular de la cuenta
            $table->string('holder_dni')->nullable(); // DNI del titular
            $table->string('bank_name')->nullable(); // Nombre del banco
            
            // Configuración
            $table->boolean('is_default')->default(false);
            $table->boolean('is_verified')->default(false);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['player_id', 'is_default']);
            $table->index(['tenant_id', 'player_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_withdrawal_accounts');
    }
};