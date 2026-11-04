<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Habit;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrudTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $this->user = $this->signIn(User::factory()->admin()->create());
    }

    public function test_all_index_pages_render(): void
    {
        foreach (['/', '/habits', '/habits/create', '/mood', '/journal', '/journal/create', '/achievements', '/profile', '/admin', '/admin/users', '/admin/categories', '/admin/tags', '/admin/achievements', '/admin/categories/create', '/admin/tags/create', '/admin/achievements/create'] as $url) {
            $this->get($url)->assertOk()->assertSee('Habitude');
        }
    }

    // ---- Categories ----

    public function test_category_can_be_created_with_generated_slug(): void
    {
        $this->post('/admin/categories', ['name' => 'Self Care', 'icon' => '🌸', 'color' => '#f97316'])
            ->assertRedirect('/admin/categories')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('categories', ['name' => 'Self Care', 'slug' => 'self-care']);
    }

    public function test_category_validation_rejects_empty_duplicate_and_bad_color(): void
    {
        Category::factory()->create(['name' => 'Mindfulness']);

        $this->post('/admin/categories', [])->assertSessionHasErrors(['name', 'color']);
        $this->post('/admin/categories', ['name' => 'Mindfulness', 'color' => '#000000'])->assertSessionHasErrors('name');
        $this->post('/admin/categories', ['name' => 'New', 'color' => 'red'])->assertSessionHasErrors('color');
    }

    public function test_category_can_be_updated_without_changing_its_name(): void
    {
        $category = Category::factory()->create(['name' => 'Read More', 'slug' => 'read-more']);

        $this->put("/admin/categories/{$category->id}", ['name' => 'Read More', 'color' => '#10b981'])
            ->assertRedirect('/admin/categories')
            ->assertSessionDoesntHaveErrors();

        $this->assertSame('#10b981', $category->fresh()->color);
    }

    public function test_category_with_habits_cannot_be_deleted(): void
    {
        $category = Category::factory()->create();
        Habit::factory()->create(['user_id' => $this->user->id, 'category_id' => $category->id]);

        $this->delete("/admin/categories/{$category->id}")->assertSessionHas('error');

        $this->assertModelExists($category);
    }

    public function test_empty_category_can_be_deleted(): void
    {
        $category = Category::factory()->create();

        $this->delete("/admin/categories/{$category->id}")->assertSessionHas('success');

        $this->assertModelMissing($category);
    }

    // ---- Tags ----

    public function test_tag_crud_and_unique_name(): void
    {
        $this->post('/admin/tags', ['name' => 'morning'])->assertRedirect('/admin/tags');
        $this->post('/admin/tags', ['name' => 'morning'])->assertSessionHasErrors('name');
        $this->post('/admin/tags', [])->assertSessionHasErrors('name');

        $tag = Tag::firstWhere('name', 'morning');
        $this->put("/admin/tags/{$tag->id}", ['name' => 'morning'])->assertSessionDoesntHaveErrors();
        $this->put("/admin/tags/{$tag->id}", ['name' => 'evening'])->assertRedirect('/admin/tags');
        $this->assertSame('evening', $tag->fresh()->name);
    }

    public function test_deleting_a_tag_detaches_it_from_habits(): void
    {
        $habit = Habit::factory()->create(['user_id' => $this->user->id]);
        $tag = Tag::factory()->create();
        $habit->tags()->attach($tag);

        $this->delete("/admin/tags/{$tag->id}")->assertSessionHas('success');

        $this->assertDatabaseMissing('habit_tag', ['tag_id' => $tag->id]);
        $this->assertModelExists($habit);
    }

    // ---- Habits ----

    public function test_habit_can_be_created_with_tags(): void
    {
        $category = Category::factory()->create();
        $tags = Tag::factory()->count(2)->create();

        $this->post('/habits', [
            'name' => 'Drink water',
            'category_id' => $category->id,
            'target_count' => 8,
            'unit' => 'glasses',
            'is_active' => '1',
            'tags' => $tags->pluck('id')->all(),
        ])->assertSessionHas('success');

        $habit = Habit::firstWhere('name', 'Drink water');
        $this->assertSame($this->user->id, $habit->user_id);
        $this->assertTrue($habit->is_active);
        $this->assertCount(2, $habit->tags);
    }

    public function test_habit_validation(): void
    {
        $this->post('/habits', [])->assertSessionHasErrors(['name', 'category_id']);
        $this->post('/habits', ['name' => 'x', 'category_id' => 999, 'target_count' => 0, 'unit' => 'times'])
            ->assertSessionHasErrors(['category_id', 'target_count']);
        $this->post('/habits', ['name' => 'x', 'category_id' => Category::factory()->create()->id, 'target_count' => 1, 'unit' => 'bananas'])
            ->assertSessionHasErrors('unit');
    }

    public function test_habit_update_syncs_tags_and_unchecked_switch_pauses_it(): void
    {
        $habit = Habit::factory()->create(['user_id' => $this->user->id, 'is_active' => true]);
        $old = Tag::factory()->create();
        $new = Tag::factory()->create();
        $habit->tags()->attach($old);

        $this->get("/habits/{$habit->id}/edit")->assertOk();

        $this->put("/habits/{$habit->id}", [
            'name' => $habit->name,
            'category_id' => $habit->category_id,
            'target_count' => 3,
            'unit' => 'times',
            'tags' => [$new->id],
        ])->assertRedirect('/habits');

        $habit->refresh();
        $this->assertFalse($habit->is_active);
        $this->assertSame([$new->id], $habit->tags->pluck('id')->all());
    }

    public function test_habit_delete_is_soft_and_keeps_its_history_for_undo(): void
    {
        $habit = Habit::factory()->create(['user_id' => $this->user->id]);
        $habit->logs()->create(['logged_date' => now()->toDateString(), 'value' => 1]);

        $this->delete("/habits/{$habit->id}")->assertSessionHas('success')->assertSessionHas('undo', route('habits.restore', $habit->id));

        $this->assertSoftDeleted($habit);
        $this->assertDatabaseCount('habit_logs', 1);

        $this->postJson("/habits/{$habit->id}/restore")->assertOk();
        $this->assertNotSoftDeleted($habit);
    }

    public function test_other_users_habits_are_not_accessible(): void
    {
        $other = User::factory()->create();
        $habit = Habit::factory()->create(['user_id' => $other->id]);

        $this->get("/habits/{$habit->id}/edit")->assertNotFound();
        $this->delete("/habits/{$habit->id}")->assertNotFound();
        $this->assertModelExists($habit);
    }
}
