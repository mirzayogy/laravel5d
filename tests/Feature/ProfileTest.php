<?php

namespace Tests\Feature;

use App\Models\Profile;
use Tests\CafeTestCase;

class ProfileTest extends CafeTestCase
{
    public function test_a_user_can_update_the_name_phone_and_address(): void
    {
        $user = $this->makeUser();
        Profile::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => 'New Name',
                'phone' => '08123456789',
                'address' => 'Jl. Ahmad Yani 1, Banjarbaru',
            ])
            ->assertRedirect(route('profile.edit'));

        $this->assertSame('New Name', $user->fresh()->name);
        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'phone' => '08123456789',
            'address' => 'Jl. Ahmad Yani 1, Banjarbaru',
        ]);
    }

    public function test_a_missing_profile_is_created_on_update(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->put(route('profile.update'), ['name' => $user->name, 'phone' => '0800', 'address' => ''])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('profiles', ['user_id' => $user->id, 'phone' => '0800']);
        $this->assertSame(1, Profile::where('user_id', $user->id)->count());
    }

    public function test_the_name_is_required(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->put(route('profile.update'), ['name' => ''])
            ->assertSessionHasErrors('name');
    }
}
