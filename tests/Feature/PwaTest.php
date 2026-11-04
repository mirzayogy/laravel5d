<?php

namespace Tests\Feature;

use App\Models\Habit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PwaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_the_manifest_makes_the_app_installable(): void
    {
        $manifest = json_decode(file_get_contents(public_path('manifest.webmanifest')), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame('standalone', $manifest['display']);
        $this->assertSame('/', $manifest['start_url']);
        $this->assertSame('#ff5900', $manifest['theme_color']);

        $sizes = collect($manifest['icons'])->mapWithKeys(fn ($i) => [$i['purpose'].':'.$i['sizes'] => $i['src']]);
        $this->assertTrue($sizes->has('any:192x192') && $sizes->has('any:512x512') && $sizes->has('maskable:512x512'));

        foreach ($manifest['icons'] as $icon) {
            $file = public_path(ltrim($icon['src'], '/'));
            $this->assertFileExists($file);
            [$w, $h] = getimagesize($file);
            $this->assertSame($icon['sizes'], "{$w}x{$h}", "{$icon['src']} is not the size the manifest says");
        }
    }

    public function test_the_service_worker_and_offline_page_exist_and_stay_safe(): void
    {
        $sw = file_get_contents(public_path('sw.js'));

        $this->assertStringContainsString("addEventListener('fetch'", $sw);
        $this->assertStringContainsString('notificationclick', $sw);
        $this->assertStringContainsString("request.method !== 'GET'", $sw);          // never touches form posts
        $this->assertStringContainsString("mode === 'navigate'", $sw);               // pages are never cached
        $this->assertStringNotContainsString('caches.put(request.url', $sw);
        $this->assertStringContainsString('You are offline', file_get_contents(public_path('offline.html')));
    }

    public function test_pages_link_the_manifest_and_theme_colour(): void
    {
        $this->signIn();

        $this->get('/')->assertSee('rel="manifest" href="/manifest.webmanifest"', false)->assertSee('name="theme-color" content="#ff5900"', false)->assertSee('apple-touch-icon', false);

        auth()->logout();
        $this->get('/login')->assertSee('rel="manifest"', false);
    }

    public function test_the_command_palette_lists_pages_actions_and_your_habits(): void
    {
        $user = $this->signIn();
        Habit::factory()->create(['user_id' => $user->id, 'name' => 'Cold shower']);
        Habit::factory()->create(['user_id' => User::factory()->create()->id, 'name' => 'Someone elses habit']);

        $html = $this->get('/')->assertOk()->assertSee('id="palette"', false)->assertSee('data-palette-open', false)->getContent();
        preg_match('#<script type="application/json" id="palette-data">(.*?)</script>#s', $html, $m);
        $labels = array_column(json_decode($m[1], true, flags: JSON_THROW_ON_ERROR), 'label');

        foreach (['Dashboard', 'Add a habit', 'Log my mood', 'Write in my journal', 'Log out', 'Cold shower'] as $expected) {
            $this->assertContains($expected, $labels);
        }
        $this->assertNotContains('Someone elses habit', $labels);   // private to its owner
        $this->assertNotContains('Admin overview', $labels);        // admin pages are for admins
    }

    public function test_admins_get_admin_shortcuts_in_the_palette(): void
    {
        $this->signIn(User::factory()->admin()->create());

        $this->get('/admin')->assertSee('Admin overview')->assertSee('Manage users');
    }

    public function test_the_add_habit_shortcut_target_exists(): void
    {
        $this->signIn();

        $this->get('/habits?add=1')->assertOk()->assertSee('id="quick-add"', false);
    }
}
