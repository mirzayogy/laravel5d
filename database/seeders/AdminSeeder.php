<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class AdminSeeder extends Seeder
{
    /**
     * Create the first administrator without touching existing data, so it is
     * safe to run on a database that already has users:
     *
     *   php artisan db:seed --class=AdminSeeder
     *
     * Credentials come from ADMIN_EMAIL / ADMIN_PASSWORD / ADMIN_NAME in .env
     * (defaults: admin@example.com / password). An existing account keeps its
     * password and is only promoted to admin. In production the default password
     * is refused: set ADMIN_PASSWORD first.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@example.com');
        $password = env('ADMIN_PASSWORD', 'password');

        if (app()->isProduction() && $password === 'password' && ! User::where('email', $email)->exists()) {
            throw new RuntimeException('Set ADMIN_PASSWORD (not "password") before creating the administrator in production.');
        }

        $admin = User::firstOrCreate(
            ['email' => $email],
            ['name' => env('ADMIN_NAME', 'Admin'), 'password' => $password],
        );

        $admin->forceFill(['role' => User::ROLE_ADMIN, 'email_verified_at' => $admin->email_verified_at ?? now()])->save();

        $admin->profile()->firstOrCreate([], ['timezone' => 'Asia/Makassar', 'onboarded_at' => now()]);

        $this->command?->info("Admin ready: {$admin->email}");
    }
}
