<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\Category;
use App\Models\Habit;
use App\Models\Tag;
use App\Models\User;
use App\Notifications\HabitReminder;
use App\Support\Greeting;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        CarbonImmutable::setTestNow('2026-10-28 03:00:00');
    }

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    private function indonesian(): User
    {
        $user = $this->signIn();
        $user->profile->update(['locale' => 'id']);

        return $user;
    }

    /** Every phrase the code asks the translator for must exist in lang/id.json. */
    public function test_every_translatable_phrase_in_the_code_has_an_indonesian_translation(): void
    {
        $dictionary = json_decode(file_get_contents(base_path('lang/id.json')), true, flags: JSON_THROW_ON_ERROR);
        $missing = [];

        $files = array_merge(
            glob(base_path('app/**/*.php')) ?: [],
            glob(base_path('app/*/*/*.php')) ?: [],
            glob(base_path('resources/views/*.blade.php')) ?: [],
            glob(base_path('resources/views/*/*.blade.php')) ?: [],
            glob(base_path('resources/views/*/*/*.blade.php')) ?: [],
            glob(base_path('resources/js/ui/*.js')) ?: [],
            [base_path('resources/js/app.js')],
        );

        foreach (array_unique($files) as $file) {
            $isJs = str_ends_with($file, '.js');
            $pattern = $isJs ? "/\\b(?:t|tr)\\('((?:[^'\\\\]|\\\\.)*)'/" : "/__\\('((?:[^'\\\\]|\\\\.)*)'/";
            preg_match_all($pattern, file_get_contents($file), $matches);

            foreach ($matches[1] as $phrase) {
                $phrase = stripslashes($phrase);
                if (! array_key_exists($phrase, $dictionary)) {
                    $missing[$phrase] = str_replace(base_path().'/', '', $file);
                }
            }
        }

        $this->assertSame([], $missing, "Add these to lang/id.json:\n".print_r($missing, true));
    }

    public function test_the_config_lists_that_are_shown_to_people_are_all_translated(): void
    {
        $dictionary = json_decode(file_get_contents(base_path('lang/id.json')), true);
        $lists = [
            config('habitude.moods'), config('habitude.criteria'), config('habitude.units'),
            collect(config('habitude.defaults'))->pluck('example')->all(),
            collect(config('habitude.starters'))->flatten(1)->pluck(0)->all(),
            array_merge(...array_values(Greeting::CATEGORY_LINES)), Greeting::DONE_LINES, Greeting::EMPTY_LINES,
            array_merge(...array_values(config('habitude.prompts'))),
            ['Health & Fitness', 'Mindfulness', 'Productivity', 'Better Sleep', 'Stay Hydrated', 'Read More', 'Social Connections', 'Self Care'],
        ];

        foreach ($lists as $list) {
            foreach ($list as $phrase) {
                $this->assertArrayHasKey($phrase, $dictionary, "Missing translation: {$phrase}");
            }
        }
    }

    public function test_english_stays_the_default_and_sends_no_dictionary(): void
    {
        $this->signIn();

        $this->get('/')->assertSee('<html lang="en"', false)->assertSee("Today's habits")->assertDontSee('window.I18N', false);
    }

    public function test_the_dashboard_speaks_indonesian_with_translated_dates(): void
    {
        $user = $this->indonesian();
        Habit::factory()->create(['user_id' => $user->id, 'target_count' => 8, 'unit' => 'glasses']);

        $this->get('/')->assertOk()
            ->assertSee('<html lang="id"', false)
            ->assertSee('Kebiasaan hari ini')
            ->assertSee('Rabu, 28 Oktober 2026')      // Wednesday, 28 October
            ->assertSee('window.I18N', false)
            ->assertSee('0 / 8 gelas')
            ->assertDontSee("Today's habits");
    }

    public function test_the_other_pages_are_translated_too(): void
    {
        $this->indonesian();

        $this->get('/habits')->assertSee('Kelompokkan menurut kategori')->assertSee('Tambah kebiasaan');
        $this->get('/mood')->assertSee('Check-in hari ini')->assertSee('Simpan suasana hati');
        $this->get('/journal')->assertSee('Jurnal')->assertSee('Catatan baru');
        $this->get('/achievements')->assertSee('Pencapaian');
        $this->get('/profile')->assertSee('Profilmu')->assertSee('Kirim pengingat lewat email');
        $this->get('/journal/create?mood=2')->assertSee('Pertanyaan untuk memulai');
    }

    public function test_seeded_names_follow_the_language_but_custom_ones_do_not(): void
    {
        $this->indonesian();
        Category::factory()->create(['name' => 'Stay Hydrated', 'slug' => 'stay-hydrated']);
        Category::factory()->create(['name' => 'Gardening', 'slug' => 'gardening']);

        $this->get('/habits/create')->assertSee('Minum Cukup Air')->assertSee('Gardening');
    }

    public function test_messages_validation_and_flash_are_indonesian(): void
    {
        $this->indonesian();

        $this->post('/habits', [])->assertSessionHasErrors(['name' => 'Nama kebiasaan wajib diisi.', 'category_id' => 'Pilih kategori.']);
        $this->post('/journal', ['entry_date' => '2026-10-28', 'title' => 'x', 'content' => 'y'])->assertSessionHas('success', 'Catatan jurnal disimpan.');
        $this->post('/mood', ['mood_level' => 9])->assertSessionHasErrors('mood_level');
        $this->assertStringContainsString('suasana hati', session('errors')->first('mood_level')); // attribute name is translated
    }

    public function test_the_greeting_and_the_reminder_email_are_indonesian(): void
    {
        $user = $this->indonesian();
        app()->setLocale('id'); // outside a request nothing has set it yet
        $category = Category::factory()->create(['slug' => 'read-more', 'name' => 'Read More']);
        $habit = Habit::factory()->create(['user_id' => $user->id, 'category_id' => $category->id, 'name' => 'Baca', 'target_count' => 10, 'unit' => 'pages']);

        $greeting = Greeting::for($user, CarbonImmutable::parse('2026-10-28 09:00:00'), collect([['habit' => $habit->load('category'), 'done' => false]]), ['current' => 0, 'best' => 0]);
        $this->assertContains($greeting['line'], ['Siap membaca beberapa halaman?', 'Siap membuka buku?', 'Siap tenggelam dalam cerita?']);

        $mail = (new HabitReminder(collect([$habit])))->toMail($user);
        $this->assertSame('Waktunya Baca', $mail->subject);
        $this->assertSame('Buka Habitude', $mail->actionText);
        $this->assertStringContainsString('10 halaman', implode(' ', $mail->introLines));
    }

    public function test_visitors_choose_a_language_on_the_sign_in_pages(): void
    {
        $this->get('/login')->assertSee('Welcome back!');
        $this->get('/login', ['Accept-Language' => 'id-ID,id;q=0.9,en;q=0.5'])->assertSee('Selamat datang kembali!');

        $this->get('/locale/id')->assertRedirect();
        $this->get('/login')->assertSee('Selamat datang kembali!')->assertSee('aria-current="true"', false);

        $this->get('/locale/en');
        $this->get('/login', ['Accept-Language' => 'id'])->assertSee('Welcome back!'); // an explicit choice beats the browser
        $this->get('/locale/fr')->assertNotFound();
    }

    public function test_the_language_is_remembered_in_the_profile(): void
    {
        $user = $this->signIn();

        $this->get('/locale/id');
        $this->assertSame('id', $user->profile->fresh()->locale);

        $this->put('/profile', ['name' => 'A', 'email' => $user->email, 'timezone' => 'UTC', 'daily_goal' => 3, 'locale' => 'en']);
        $this->assertSame('en', $user->profile->fresh()->locale);
        $this->put('/profile', ['name' => 'A', 'email' => $user->email, 'timezone' => 'UTC', 'daily_goal' => 3, 'locale' => 'xx'])->assertSessionHasErrors('locale');
    }

    public function test_the_skyline_and_scripts_get_the_right_locale(): void
    {
        $this->indonesian();

        $this->get('/')->assertSee('&quot;locale&quot;:&quot;id-ID&quot;', false);
    }

    /** A quote inside a Blade attribute once leaked "$category->name])" onto the page. */
    public function test_no_page_shows_template_code_in_either_language(): void
    {
        $admin = $this->signIn(User::factory()->admin()->create());
        $category = Category::factory()->create();
        Habit::factory()->create(['user_id' => $admin->id, 'category_id' => $category->id]);
        $admin->journalEntries()->create(['entry_date' => '2026-10-28', 'title' => 'Entry', 'content' => 'Text']);
        $admin->moodEntries()->create(['entry_date' => '2026-10-28', 'mood_level' => 3]);
        Tag::factory()->create();
        Achievement::create(['name' => 'Test badge', 'criteria' => 'logs:1']);

        foreach (['en', 'id'] as $locale) {
            $admin->profile->update(['locale' => $locale]);

            foreach (['/', '/habits', '/habits?view=grid', '/journal', '/mood', '/achievements', '/admin', '/admin/users', '/admin/categories', '/admin/tags', '/admin/achievements'] as $url) {
                $html = $this->get($url)->assertOk()->getContent();
                $visible = strip_tags(preg_replace('#<(script|style)\b.*?</\1>#s', '', $html));

                $this->assertDoesNotMatchRegularExpression('/\$\w+->|\]\)"|@\w+\(|\{\{|__\(/', $visible, "Template code leaked on {$url} ({$locale})");
            }
        }
    }

    /**
     * A local variable called `t` in a script that imports the translator `t` throws as soon as the
     * translator is used ("Cannot access 't' before initialization"). In tracker.js that made every
     * check-in look like a failed save, so no script may do it.
     */
    public function test_no_script_shadows_the_imported_translator(): void
    {
        $checked = 0;

        foreach (glob(resource_path('js/{,ui/}*.js'), GLOB_BRACE) as $file) {
            $code = file_get_contents($file);

            if (! preg_match('/import\s*\{[^}]*\bt\b(?!\s+as)[^}]*\}\s*from\s*[\'"]\.\/i18n\.js[\'"]/', $code)) {
                continue;
            }

            $checked++;
            $this->assertDoesNotMatchRegularExpression('/\b(const|let|var)\s+t\b/', $code, basename($file).' declares its own `t`');
        }

        $this->assertGreaterThan(5, $checked);
    }
}
