<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Customer-neutral and read-only counts. Deliberately excludes amounts,
 * balances, users, tokens, payloads and private logging details.
 */
class RedByteMaintenanceEvidence
{
    public function collect(CarbonImmutable $startLocal, CarbonImmutable $endLocal): array
    {
        $tz = (string) config('redbyte_maintenance.timezone');
        $fromUtc = $startLocal->startOfDay()->utc();
        $untilUtc = $endLocal->addDay()->startOfDay()->utc();
        $observedAt = CarbonImmutable::now('UTC');

        $observed = [
            'database' => 'unavailable',
            'operators_active_now' => null,
            'players_registered_now' => null,
            'transactions_registered_now' => null,
        ];
        $period = [
            'operators_created_in_period' => null,
            'players_created_in_period' => null,
            'transactions_created_in_period' => null,
        ];

        try {
            DB::selectOne('SELECT 1');
            $observed['database'] = 'reachable';

            $observed['operators_active_now'] = DB::table('tenants')
                ->whereNull('deleted_at')->where('is_active', true)->count();
            $observed['players_registered_now'] = DB::table('players')
                ->whereNull('deleted_at')->count();
            $observed['transactions_registered_now'] = DB::table('transactions')->count();

            foreach ([
                'operators_created_in_period' => 'tenants',
                'players_created_in_period' => 'players',
                'transactions_created_in_period' => 'transactions',
            ] as $key => $table) {
                $period[$key] = DB::table($table)
                    ->where('created_at', '>=', $fromUtc->format('Y-m-d H:i:s'))
                    ->where('created_at', '<', $untilUtc->format('Y-m-d H:i:s'))
                    ->count();
            }
        } catch (Throwable $exception) {
            // A failed query must not be reported as zero transactions or users.
            $observed['database'] = 'unavailable_or_partial';
        }

        return [
            'schema_version' => 'redbyte.maintenance-evidence.v1',
            'client' => 'nextlevel',
            'collected_at_utc' => $observedAt->toIso8601String(),
            'window' => [
                'start' => $startLocal->toDateString(),
                'end' => $endLocal->toDateString(),
                'timezone' => $tz,
                'inclusive' => true,
            ],
            'snapshot' => $observed,
            'activity' => $period,
            'external_sources' => [
                'uptimerobot' => 'collected_by_redbyte_centrally',
                'tickets_and_interventions' => 'collected_by_redbyte_centrally',
                'server_backups_and_errors' => 'not_available_from_this_endpoint',
            ],
            'limitations' => [
                'Snapshot values describe collection time and cannot establish historical uptime.',
                'Activity counts use the records creation timestamps; they do not prove user activity.',
                'Database fields are assumed stored in UTC and reporting dates use the configured timezone.',
                'This API never exports individual transactions, money amounts or personal data.',
                'This response is evidence, not the ReportDraftService import schema.',
            ],
        ];
    }
}
