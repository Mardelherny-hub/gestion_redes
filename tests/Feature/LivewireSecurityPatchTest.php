<?php

namespace Tests\Feature;

use App\Livewire\Auth\Login;
use Composer\InstalledVersions;
use Livewire\Component;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Smoke test isolated from customer and production data.
 * Verifies that the patched Livewire runtime can render and process
 * component actions using the test PostgreSQL database.
 */
class LivewireSecurityPatchTest extends TestCase
{
    public function test_livewire_is_on_the_patched_3_x_release_line(): void
    {
        $installed = ltrim((string) InstalledVersions::getPrettyVersion('livewire/livewire'), 'v');

        $this->assertTrue(
            version_compare($installed, '3.8.3', '>=')
                && version_compare($installed, '4.0.0', '<'),
            'Livewire must be patched without migrating to v4.'
        );
    }

    public function test_livewire_component_can_render_and_handle_an_action(): void
    {
        Livewire::test(LivewireSecurityPatchCounter::class)
            ->assertSet('count', 0)
            ->call('increase')
            ->assertSet('count', 1);
    }

    public function test_real_login_form_renders_and_rejects_unknown_credentials(): void
    {
        Livewire::test(Login::class)
            ->assertStatus(200)
            ->set('credential', 'qa-nobody@example.invalid')
            ->set('password', 'invalid-testing-only')
            ->call('login')
            ->assertHasErrors(['credential']);
    }

    public function test_livewire_javascript_is_served(): void
    {
        $response = $this->get('/livewire/livewire.js');

        $response->assertStatus(200);
        $this->assertGreaterThan(
            1000,
            strlen($response->streamedContent()),
            'Livewire JavaScript bundle should not be empty.'
        );
    }
}

class LivewireSecurityPatchCounter extends Component
{
    public int $count = 0;

    public function increase(): void
    {
        $this->count++;
    }

    public function render(): string
    {
        return '<div>{{ $count }}</div>';
    }
}
