<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RedByteMaintenanceEvidenceTest extends TestCase
{
    private const TOKEN = 'read-only-test-token-48-characters-1234567890000';

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('redbyte_maintenance.enabled', false);
        config()->set('redbyte_maintenance.read_token', '');
        config()->set('redbyte_maintenance.timezone', 'America/Argentina/Buenos_Aires');
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');

        Schema::connection('sqlite')->create('tenants', function (Blueprint $table): void {
            $table->id();
            $table->boolean('is_active');
            $table->timestamp('created_at');
            $table->timestamp('deleted_at')->nullable();
        });
        Schema::connection('sqlite')->create('players', function (Blueprint $table): void {
            $table->id();
            $table->string('phone');
            $table->timestamp('created_at');
            $table->timestamp('deleted_at')->nullable();
        });
        Schema::connection('sqlite')->create('transactions', function (Blueprint $table): void {
            $table->id();
            $table->string('notes')->nullable();
            $table->decimal('amount');
            $table->timestamp('created_at');
        });
    }

    protected function tearDown(): void
    {
        DB::disconnect('sqlite');
        parent::tearDown();
    }

    private function url(): string
    {
        return '/api/internal/maintenance-evidence/v1?from=2026-09-05&to=2026-10-04';
    }

    public function test_endpoint_is_off_by_default_and_fails_closed_for_missing_weak_or_invalid_tokens(): void
    {
        $this->getJson($this->url())->assertNotFound();
        config()->set('redbyte_maintenance.enabled', true);
        config()->set('redbyte_maintenance.read_token', 'short');
        $this->getJson($this->url(), ['X-RedByte-Evidence-Token' => 'short'])->assertNotFound();
        config()->set('redbyte_maintenance.read_token', self::TOKEN);
        $this->getJson($this->url())->assertNotFound();
        $this->getJson($this->url(), ['X-RedByte-Evidence-Token' => str_repeat('z', 48)])->assertNotFound();
    }

    public function test_valid_token_returns_only_aggregated_counts_and_exact_window(): void
    {
        config()->set('redbyte_maintenance.enabled', true);
        config()->set('redbyte_maintenance.read_token', self::TOKEN);
        DB::table('tenants')->insert([
            ['id' => 1, 'is_active' => true, 'created_at' => '2026-09-07 10:00:00', 'deleted_at' => null],
            ['id' => 2, 'is_active' => false, 'created_at' => '2026-09-10 10:00:00', 'deleted_at' => null],
            ['id' => 3, 'is_active' => true, 'created_at' => '2026-07-10 10:00:00', 'deleted_at' => null],
        ]);
        DB::table('players')->insert([
            ['id' => 1, 'phone' => '555-SECRET', 'created_at' => '2026-09-05 10:00:00', 'deleted_at' => null],
            ['id' => 2, 'phone' => '555-OTHER', 'created_at' => '2026-10-04 12:00:00', 'deleted_at' => null],
            ['id' => 3, 'phone' => '555-DELETED', 'created_at' => '2026-06-01 12:00:00', 'deleted_at' => '2026-10-01 12:00:00'],
        ]);
        DB::table('transactions')->insert([
            ['id' => 1, 'notes' => 'private customer information', 'amount' => 50000, 'created_at' => '2026-09-06 10:00:00'],
            ['id' => 2, 'notes' => 'not in period', 'amount' => 100, 'created_at' => '2026-08-06 10:00:00'],
        ]);

        $before = [DB::table('tenants')->count(), DB::table('players')->count(), DB::table('transactions')->count()];
        $response = $this->getJson($this->url(), ['X-RedByte-Evidence-Token' => self::TOKEN]);
        $response->assertOk()
            ->assertJsonPath('schema_version', 'redbyte.maintenance-evidence.v1')
            ->assertJsonPath('client', 'nextlevel')
            ->assertJsonPath('snapshot.database', 'reachable')
            ->assertJsonPath('snapshot.operators_active_now', 2)
            ->assertJsonPath('snapshot.players_registered_now', 2)
            ->assertJsonPath('snapshot.transactions_registered_now', 2)
            ->assertJsonPath('activity.operators_created_in_period', 2)
            ->assertJsonPath('activity.players_created_in_period', 2)
            ->assertJsonPath('activity.transactions_created_in_period', 1)
            ->assertJsonPath('window.start', '2026-09-05')
            ->assertJsonPath('window.end', '2026-10-04');

        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $json = $response->getContent();
        foreach ([self::TOKEN, '555-SECRET', '555-OTHER', '50000', 'private customer information'] as $secret) {
            $this->assertStringNotContainsString($secret, $json);
        }
        $this->assertSame($before, [DB::table('tenants')->count(), DB::table('players')->count(), DB::table('transactions')->count()]);
    }

    public function test_missing_invalid_future_and_excessive_periods_rejected(): void
    {
        config()->set('redbyte_maintenance.enabled', true);
        config()->set('redbyte_maintenance.read_token', self::TOKEN);
        $headers = ['X-RedByte-Evidence-Token' => self::TOKEN];
        $path = '/api/internal/maintenance-evidence/v1';
        $this->getJson($path, $headers)->assertUnprocessable();
        $this->getJson($path.'?from=2026-02-30&to=2026-03-02', $headers)->assertUnprocessable();
        $this->getJson($path.'?from=2026-10-04&to=2026-09-05', $headers)->assertUnprocessable();
        $this->getJson($path.'?from=2026-01-01&to=2026-10-04', $headers)->assertUnprocessable();
        $this->getJson($path.'?from=2099-01-01&to=2099-01-02', $headers)->assertUnprocessable();
    }
}
