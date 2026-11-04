<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\Habit;
use App\Models\User;
use App\Support\Avatars;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    private function admin(): User
    {
        return $this->signIn(User::factory()->admin()->create(['name' => 'Root Admin']));
    }

    public function test_guests_are_sent_to_login_and_regular_users_get_403(): void
    {
        $this->get('/admin')->assertRedirect('/login');

        $this->signIn();
        foreach (['/admin', '/admin/users', '/admin/categories', '/admin/tags', '/admin/achievements', '/admin/categories/create'] as $url) {
            $this->get($url)->assertForbidden();
        }
        $this->post('/admin/categories', ['name' => 'Sneaky', 'color' => '#000000'])->assertForbidden();
        $this->assertDatabaseMissing('categories', ['name' => 'Sneaky']);
        $this->delete('/admin/users/'.User::factory()->create()->id)->assertForbidden();
    }

    public function test_admin_overview_shows_system_numbers(): void
    {
        $this->admin();
        $habit = Habit::factory()->create(['user_id' => User::factory()->create()->id, 'target_count' => 1]);
        $habit->logs()->create(['logged_date' => now()->toDateString(), 'value' => 1]);

        $this->get('/admin')->assertOk()
            ->assertSee('Admin console')
            ->assertSee('Check-ins')
            ->assertSee('Release stage')
            ->assertSee($habit->category->name);
    }

    public function test_admin_can_search_and_filter_users(): void
    {
        $this->admin();
        User::factory()->create(['name' => 'Budi Santoso', 'email' => 'budi@example.com']);
        User::factory()->create(['name' => 'Citra Dewi', 'email' => 'citra@example.com']);

        $this->get('/admin/users?q=budi')->assertOk()->assertSee('Budi Santoso')->assertDontSee('Citra Dewi');
        $this->get('/admin/users?role=admin')->assertOk()->assertSee('Root Admin')->assertDontSee('Budi Santoso');
        $this->get('/admin/users?role=nonsense')->assertOk()->assertSee('Budi Santoso'); // bad filter is ignored
    }

    public function test_admin_can_edit_a_user_and_promote_them(): void
    {
        $this->admin();
        $user = User::factory()->create();

        $this->get("/admin/users/{$user->id}/edit")->assertOk();
        $this->put("/admin/users/{$user->id}", ['name' => 'Renamed', 'email' => 'renamed@example.com', 'role' => 'admin', 'password' => 'a-new-password'])
            ->assertRedirect('/admin/users');

        $user->refresh();
        $this->assertSame('Renamed', $user->name);
        $this->assertTrue($user->isAdmin());
        $this->assertTrue(\Hash::check('a-new-password', $user->password));

        $this->put("/admin/users/{$user->id}", ['name' => 'X', 'email' => 'x@example.com', 'role' => 'superuser'])->assertSessionHasErrors('role');
        $this->put("/admin/users/{$user->id}", ['name' => 'X', 'email' => 'x@example.com', 'role' => 'user', 'password' => 'short'])->assertSessionHasErrors('password');
    }

    public function test_role_is_not_mass_assignable_through_registration_or_profile(): void
    {
        $this->post('/register', ['name' => 'Eve', 'email' => 'eve@example.com', 'password' => 'secret-pass', 'password_confirmation' => 'secret-pass', 'role' => 'admin']);
        $this->assertFalse(User::firstWhere('email', 'eve@example.com')->isAdmin());

        $user = $this->signIn();
        $this->put('/profile', ['name' => 'Eve', 'email' => $user->email, 'timezone' => 'UTC', 'daily_goal' => 3, 'role' => 'admin']);
        $this->assertFalse($user->fresh()->isAdmin());
    }

    public function test_admin_cannot_change_their_own_role_or_delete_themselves(): void
    {
        $admin = $this->admin();

        $this->put("/admin/users/{$admin->id}", ['name' => 'Root', 'email' => $admin->email, 'role' => 'user'])->assertSessionHasErrors('role');
        $this->assertTrue($admin->fresh()->isAdmin());

        $this->delete("/admin/users/{$admin->id}")->assertSessionHas('error');
        $this->assertModelExists($admin);
    }

    public function test_deleting_a_user_removes_all_of_their_data(): void
    {
        $this->admin();
        $user = User::factory()->create();
        $user->profile()->create(['timezone' => 'UTC']);
        $habit = Habit::factory()->create(['user_id' => $user->id]);
        $habit->logs()->create(['logged_date' => now()->toDateString(), 'value' => 1]);
        $habit->reminders()->create(['remind_at' => '08:00', 'days_of_week' => [1]]);
        $user->moodEntries()->create(['entry_date' => now()->toDateString(), 'mood_level' => 3]);
        $user->journalEntries()->create(['entry_date' => now()->toDateString(), 'title' => 't', 'content' => 'c']);
        $user->achievements()->attach(Achievement::create(['name' => 'A', 'criteria' => 'logs:1']), ['earned_at' => now()]);

        $this->delete("/admin/users/{$user->id}")->assertSessionHas('success');

        $this->assertModelMissing($user);
        foreach (['habits', 'habit_logs', 'reminders', 'mood_entries', 'journal_entries', 'achievement_user'] as $table) {
            $this->assertDatabaseCount($table, 0);
        }
        $this->assertDatabaseCount('profiles', 1); // only the signed-in admin's own profile remains
        $this->assertDatabaseCount('achievements', 1); // shared master data stays
    }

    public function test_master_data_is_managed_by_admins_but_readable_by_users(): void
    {
        $this->admin();
        $this->post('/admin/categories', ['name' => 'Gardening', 'color' => '#00aa00'])->assertRedirect('/admin/categories');
        Achievement::create(['name' => 'Green thumb', 'description' => 'Water the plants', 'criteria' => 'logs:5']);

        $this->get('/admin/achievements')->assertOk()->assertSee('Green thumb')->assertSee('Add achievement')->assertSee('delete-achievement', false);

        $this->signIn();
        $this->get('/achievements')->assertOk()->assertSee('Green thumb')->assertDontSee('Add achievement')->assertDontSee('delete-achievement', false);
        $this->get('/habits/create')->assertOk()->assertSee('Gardening');
    }

    public function test_release_stage_label_and_banner(): void
    {
        $this->signIn();

        $this->get('/')->assertSee('Beta preview, not production', false)->assertSee('Beta', false)->assertDontSee('Admin console');

        config(['habitude.stage' => 'production']);
        $this->get('/')->assertDontSee('Beta preview')->assertDontSee('>Beta<', false);
    }

    public function test_admin_pages_carry_the_admin_label_and_banner(): void
    {
        $this->admin();

        $this->get('/admin')->assertSee('Admin console. Changes here affect every user', false)->assertSee('>Admin<', false);
        $this->get('/')->assertSee('Admin console')->assertSee('>Beta<', false); // menu link + stage badge on the user side
    }

    public function test_admin_seeder_is_safe_to_run_on_a_database_with_data(): void
    {
        $existing = User::factory()->create(['email' => 'someone@example.com']);

        $this->seed(AdminSeeder::class);
        $admin = User::firstWhere('email', 'admin@example.com');
        $this->assertTrue($admin->isAdmin());
        $this->assertNotNull($admin->profile->onboarded_at);
        $this->assertModelExists($existing);

        // running it again keeps the same account and password
        $hash = $admin->password;
        $this->seed(AdminSeeder::class);
        $this->assertSame(1, User::where('email', 'admin@example.com')->count());
        $this->assertSame($hash, $admin->fresh()->password);

        // and the account can actually log in
        $this->post('/login', ['email' => 'admin@example.com', 'password' => 'password'])->assertRedirect('/');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_avatars_are_animated_waves_that_stay_the_same_per_user(): void
    {
        $a = Avatars::svg('user-1');
        $b = Avatars::svg('user-2');

        $this->assertStringContainsString('<svg', $a);
        $this->assertStringContainsString('dbwa-l1', $a);            // layers the CSS animation moves
        $this->assertStringContainsString('@keyframes', $a);          // the "slow" animation variant is on
        $this->assertStringContainsString('prefers-reduced-motion', $a);
        $this->assertStringNotContainsString('<metadata', $a);
        $this->assertSame($a, Avatars::svg('user-1')); // deterministic
        $this->assertNotSame($a, $b);                                // different people look different
    }

    public function test_avatar_shows_in_the_sidebar_profile_and_admin_list(): void
    {
        $admin = $this->admin();
        $other = User::factory()->create(['name' => 'Budi Santoso']);

        $this->get('/')->assertSee('Avatar of Root Admin', false);
        $this->get('/profile')->assertSee('Avatar of Root Admin', false);
        $this->get('/admin/users')->assertSee('Avatar of Budi Santoso', false)->assertSee('dbwa-l1', false);
        $this->get("/admin/users/{$other->id}/edit")->assertSee('Avatar of Budi Santoso', false);
    }

    public function test_make_admin_command_grants_and_revokes_the_role(): void
    {
        $user = User::factory()->create(['email' => 'promote@example.com']);

        $this->artisan('habitude:make-admin', ['email' => 'promote@example.com'])->assertSuccessful();
        $this->assertTrue($user->fresh()->isAdmin());

        $this->artisan('habitude:make-admin', ['email' => 'promote@example.com', '--revoke' => true])->assertSuccessful();
        $this->assertFalse($user->fresh()->isAdmin());

        $this->artisan('habitude:make-admin', ['email' => 'nobody@example.com'])->assertFailed();
    }
}
