<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Create a user who finished onboarding, and sign in as them.
     */
    protected function signIn(?User $user = null): User
    {
        $user ??= User::factory()->create();
        $user->profile()->firstOrCreate([], ['timezone' => 'Asia/Makassar', 'onboarded_at' => now()]);
        $this->actingAs($user);

        return $user;
    }
}
