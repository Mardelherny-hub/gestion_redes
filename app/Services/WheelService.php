<?php

namespace App\Services;

use App\Models\Player;
use App\Models\WheelSpin;
use Illuminate\Support\Facades\DB;
use App\Models\WheelConfig;

class WheelService
{
    protected $bonusService;
    protected $messageService;

    public function __construct(BonusService $bonusService, MessageService $messageService)
    {
        $this->bonusService = $bonusService;
        $this->messageService = $messageService;
    }

    /**
     * Verificar si el jugador puede girar hoy
     */
    public function canSpinToday(Player $player): bool
    {
        $config = $this->getWheelConfig($player->tenant_id);
        
        // Verificar si la ruleta está activa
        if (!$config['is_active']) {
            return false;
        }
        
        $todaySpins = WheelSpin::where('player_id', $player->id)
            ->whereDate('created_at', today())
            ->count();
        
        return $todaySpins < $config['daily_limit'];
    }

    /**
     * Obtener configuración de premios del tenant
     */
    public function getPrizeConfiguration(?int $tenantId = null): array
    {
        $config = $this->getWheelConfig($tenantId);
        return $config['segments'];
    }

    /**
     * Obtener configuración de la ruleta para un tenant
     */
    public function getWheelConfig(?int $tenantId = null): array
    {
        if ($tenantId) {
            $config = WheelConfig::where('tenant_id', $tenantId)->first();
            
            if ($config) {
                return [
                    'is_active' => $config->is_active,
                    'daily_limit' => $config->daily_limit,
                    'segments' => $config->segments,
                ];
            }
        }
        
        // Fallback: configuración por defecto
        return [
            'is_active' => true,
            'daily_limit' => 1,
            'segments' => WheelConfig::getDefaultSegments(),
        ];
    }

    /**
     * Girar la ruleta
     */
    public function spin(Player $player): array
    {
        if (!$this->canSpinToday($player)) {
            throw new \Exception('Ya giraste la ruleta hoy. Vuelve mañana!');
        }

        return DB::transaction(function () use ($player) {
            // Seleccionar premio basado en probabilidades
            $prize = $this->selectPrize($player->tenant_id);
            
            // Crear registro del giro
            $spin = WheelSpin::create([
                'tenant_id' => $player->tenant_id,
                'player_id' => $player->id,
                'prize_amount' => $prize['amount'],
                'prize_type' => $prize['type'],
                'prize_description' => $prize['label'],
            ]);

            // Procesar el premio
            $this->processPrize($player, $prize, $spin);

            return [
                'prize' => $prize,
                'spin' => $spin,
            ];
        });
    }

    /**
     * Seleccionar premio basado en probabilidades
     */
    protected function selectPrize(?int $tenantId = null): array
    {
        $prizes = $this->getPrizeConfiguration($tenantId);
        $totalProbability = array_sum(array_column($prizes, 'probability'));
        $random = rand(1, $totalProbability);
        
        $currentProbability = 0;
        foreach ($prizes as $prize) {
            $currentProbability += $prize['probability'];
            if ($random <= $currentProbability) {
                return $prize;
            }
        }
        
        // Fallback (no debería llegar aquí)
        return end($prizes);
    }

    /**
     * Procesar el premio ganado
     */
    protected function processPrize(Player $player, array $prize, WheelSpin $spin): void
    {
        switch ($prize['type']) {
            case 'cash':
                // Agregar dinero directo al saldo
                $player->increment('balance', $prize['amount']);
                
                // Mensaje
                $this->messageService->sendSystemMessage(
                    $player,
                    "🎰 ¡Felicitaciones! Ganaste \${$prize['amount']} en la ruleta. El dinero ya está en tu saldo.",
                    'bonus'
                );
                break;
                
            case 'bonus':
                // Otorgar bono
                $bonus = $this->bonusService->grantBonus(
                    $player,
                    'spin_wheel',
                    $prize['amount'],
                    "Premio de ruleta: {$prize['label']}"
                );
                
                $spin->update(['bonus_id' => $bonus->id]);
                break;
                
            case 'free_spin':
                // TODO: implementar giro extra (por ahora solo mensaje)
                $this->messageService->sendSystemMessage(
                    $player,
                    "🎰 ¡Ganaste un giro extra! Vuelve mañana para otro intento.",
                    'bonus'
                );
                break;
                
            case 'nothing':
                // Mensaje de ánimo
                $this->messageService->sendSystemMessage(
                    $player,
                    "🎰 No ganaste esta vez, pero puedes intentar mañana. ¡Suerte!",
                    'general'
                );
                break;
        }

        // Activity log
        activity()
            ->performedOn($spin)
            ->causedBy($player)
            ->withProperties([
                'prize_type' => $prize['type'],
                'prize_amount' => $prize['amount'],
            ])
            ->log("Giró la ruleta y ganó: {$prize['label']}");
    }

    /**
     * Obtener historial de giros del jugador
     */
    public function getPlayerSpins(Player $player, int $limit = 10)
    {
        return WheelSpin::where('player_id', $player->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}