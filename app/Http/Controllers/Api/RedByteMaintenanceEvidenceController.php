<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RedByteMaintenanceEvidence;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class RedByteMaintenanceEvidenceController extends Controller
{
    public function show(Request $request, RedByteMaintenanceEvidence $collector): JsonResponse
    {
        $enabled = filter_var(config('redbyte_maintenance.enabled'), FILTER_VALIDATE_BOOLEAN);
        $expected = (string) config('redbyte_maintenance.read_token');
        $provided = (string) $request->header('X-RedByte-Evidence-Token', '');

        // Fail closed even when deployed accidentally without a secret.
        abort_unless($enabled && strlen($expected) >= 32 && $provided !== '' && hash_equals($expected, $provided), 404);

        $input = Validator::make($request->query(), [
            'from' => ['required', 'date_format:Y-m-d'],
            'to' => ['required', 'date_format:Y-m-d'],
        ])->validate();

        $timezone = (string) config('redbyte_maintenance.timezone');
        try {
            $from = CarbonImmutable::createFromFormat('!Y-m-d', $input['from'], $timezone);
            $to = CarbonImmutable::createFromFormat('!Y-m-d', $input['to'], $timezone);
        } catch (Throwable $exception) {
            return response()->json(['error' => 'invalid_period'], 422);
        }

        if (! $from || ! $to || $from->format('Y-m-d') !== $input['from'] ||
            $to->format('Y-m-d') !== $input['to'] ||
            $from->greaterThan($to) || $from->diffInDays($to) > 62 ||
            $to->greaterThan(CarbonImmutable::now($timezone)->startOfDay())) {
            return response()->json(['error' => 'invalid_period'], 422);
        }

        return response()->json($collector->collect($from, $to))
            ->withHeaders([
                'Cache-Control' => 'private, no-store, max-age=0',
                'Pragma' => 'no-cache',
                'X-Robots-Tag' => 'noindex, nofollow',
            ]);
    }
}
