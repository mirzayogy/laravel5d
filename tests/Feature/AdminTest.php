<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Tag;
use Tests\CafeTestCase;

class AdminTest extends CafeTestCase
{
    public function test_an_admin_can_create_a_menu_with_tags(): void
    {
        $category = Category::factory()->create();
        $spicy = Tag::factory()->create();
        $hot = Tag::factory()->create();

        $this->actingAs($this->makeUser('admin'))
            ->post(route('admin.menus.store'), [
                'category_id' => $category->id,
                'name' => 'Chicken Rice',
                'description' => 'With sambal',
                'price' => 22000,
                'is_available' => '1',
                'tag_ids' => [$spicy->id, $hot->id],
            ])
            ->assertRedirect(route('admin.menus.index'));

        $menu = Menu::where('name', 'Chicken Rice')->first();
        $this->assertNotNull($menu);
        $this->assertTrue($menu->is_available);
        $this->assertSame(22000, $menu->price);
        $this->assertSame(2, $menu->tags()->count());
    }

    public function test_a_menu_needs_a_valid_price_and_category(): void
    {
        $this->actingAs($this->makeUser('admin'))
            ->post(route('admin.menus.store'), [
                'category_id' => 9999,
                'name' => 'Broken',
                'price' => -5,
            ])
            ->assertSessionHasErrors(['category_id', 'price']);

        $this->assertSame(0, Menu::count());
    }

    public function test_an_admin_can_mark_a_menu_as_unavailable_and_change_its_tags(): void
    {
        $menu = $this->makeMenu(['is_available' => true]);
        $tag = Tag::factory()->create();
        $menu->tags()->attach($tag->id);

        $this->actingAs($this->makeUser('admin'))
            ->put(route('admin.menus.update', $menu), [
                'category_id' => $menu->category_id,
                'name' => $menu->name,
                'price' => $menu->price,
            ])
            ->assertRedirect(route('admin.menus.index'));

        $menu->refresh();
        $this->assertFalse($menu->is_available);
        $this->assertSame(0, $menu->tags()->count());
    }

    public function test_a_menu_used_in_orders_cannot_be_deleted(): void
    {
        $menu = $this->makeMenu();
        $this->makeOrder($this->makeUser(), [$menu]);

        $this->actingAs($this->makeUser('admin'))
            ->delete(route('admin.menus.destroy', $menu))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('menus', ['id' => $menu->id]);
    }

    public function test_an_unused_menu_can_be_deleted(): void
    {
        $menu = $this->makeMenu();

        $this->actingAs($this->makeUser('admin'))
            ->delete(route('admin.menus.destroy', $menu))
            ->assertRedirect(route('admin.menus.index'));

        $this->assertDatabaseMissing('menus', ['id' => $menu->id]);
    }

    public function test_an_admin_can_create_rename_and_delete_a_category(): void
    {
        $admin = $this->makeUser('admin');

        $this->actingAs($admin)->post(route('admin.categories.store'), ['name' => 'Bakery'])->assertSessionHas('success');
        $category = Category::where('name', 'Bakery')->first();
        $this->assertNotNull($category);

        $this->actingAs($admin)->put(route('admin.categories.update', $category), ['name' => 'Bread'])->assertSessionHas('success');
        $this->assertSame('Bread', $category->fresh()->name);

        $this->actingAs($admin)->delete(route('admin.categories.destroy', $category))->assertSessionHas('success');
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_a_category_name_must_be_unique(): void
    {
        Category::factory()->create(['name' => 'Coffee']);

        $this->actingAs($this->makeUser('admin'))
            ->post(route('admin.categories.store'), ['name' => 'Coffee'])
            ->assertSessionHasErrors('name');
    }

    public function test_a_category_with_menus_cannot_be_deleted(): void
    {
        $menu = $this->makeMenu();

        $this->actingAs($this->makeUser('admin'))
            ->delete(route('admin.categories.destroy', $menu->category_id))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('categories', ['id' => $menu->category_id]);
    }

    public function test_an_admin_can_create_and_delete_a_tag(): void
    {
        $admin = $this->makeUser('admin');

        $this->actingAs($admin)->post(route('admin.tags.store'), ['name' => 'Vegan'])->assertSessionHas('success');
        $tag = Tag::where('name', 'Vegan')->first();
        $this->assertNotNull($tag);

        $this->actingAs($admin)->delete(route('admin.tags.destroy', $tag))->assertSessionHas('success');
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }

    public function test_a_customer_cannot_manage_the_menu(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($this->makeUser('customer'))
            ->post(route('admin.menus.store'), [
                'category_id' => $category->id,
                'name' => 'Nope',
                'price' => 1000,
            ])
            ->assertForbidden();

        $this->assertSame(0, Menu::count());
    }
}
