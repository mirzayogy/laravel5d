<?php

namespace Tests\Feature;

use App\Models\Writeup;
use Tests\FlagboardTestCase;

class MarkdownTest extends FlagboardTestCase
{
    public function test_the_preview_renders_markdown_and_strips_html(): void
    {
        $response = $this->actingAs($this->makePlayer())
            ->postJson(route('writeups.preview'), ['content' => "# Hi\n\n<script>alert(1)</script>\n\n**bold**"])
            ->assertOk();

        $html = $response->json('html');
        $this->assertStringContainsString('<h1>Hi</h1>', $html);
        $this->assertStringContainsString('<strong>bold</strong>', $html);
        $this->assertStringNotContainsString('<script>', $html);
    }

    public function test_a_writeup_is_shown_as_html(): void
    {
        $writeup = Writeup::factory()->create(['status' => 'published', 'content' => "## Steps\n\n- one\n- two\n\n<script>alert(1)</script>"]);

        $this->get(route('writeups.show', $writeup))
            ->assertOk()
            ->assertSee('<h2>Steps</h2>', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_a_challenge_description_is_rendered_as_markdown(): void
    {
        $competition = $this->makeCompetition();
        $user = $this->makePlayer();
        $this->register($user->team, $competition);
        $challenge = $this->makeChallenge($competition, 'FLAG{m}', ['description' => 'Find the **hidden** flag.']);

        $this->actingAs($user)->get(route('challenges.show', $challenge))->assertOk()->assertSee('<strong>hidden</strong>', false);
    }

    public function test_the_writeup_list_can_be_searched(): void
    {
        Writeup::factory()->create(['status' => 'published', 'title' => 'Needle in a haystack']);
        Writeup::factory()->create(['status' => 'published', 'title' => 'Something else']);

        $this->get(route('writeups.index', ['q' => 'Needle']))
            ->assertOk()
            ->assertSee('Needle in a haystack')
            ->assertDontSee('Something else');
    }
}
