<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminSeeder;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class SeederSafetyTest extends TestCase
{
    use RefreshDatabase;

    private function production(): void
    {
        $this->app['env'] = 'production';
    }

    public function test_the_demo_seeder_refuses_to_run_in_production(): void
    {
        $this->production();

        $this->expectException(RuntimeException::class);
        (new DatabaseSeeder)->run();
    }

    public function test_the_admin_seeder_refuses_the_default_password_in_production(): void
    {
        $this->production();

        try {
            (new AdminSeeder)->run();
            $this->fail('Expected the seeder to refuse the default password.');
        } catch (RuntimeException) {
            $this->assertDatabaseCount('users', 0);
        }
    }

    public function test_the_admin_seeder_accepts_its_own_password_in_production(): void
    {
        $this->production();
        putenv('ADMIN_PASSWORD=a-long-private-passphrase');
        $_ENV['ADMIN_PASSWORD'] = $_SERVER['ADMIN_PASSWORD'] = 'a-long-private-passphrase';

        try {
            (new AdminSeeder)->run();
        } finally {
            putenv('ADMIN_PASSWORD');
            unset($_ENV['ADMIN_PASSWORD'], $_SERVER['ADMIN_PASSWORD']);
        }

        $this->assertTrue(User::where('email', 'admin@example.com')->firstOrFail()->isAdmin());
    }

    public function test_local_seeding_still_works(): void
    {
        (new AdminSeeder)->run();

        $this->assertTrue(User::where('email', 'admin@example.com')->firstOrFail()->isAdmin());
    }
}
