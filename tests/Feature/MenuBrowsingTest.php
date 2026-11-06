<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Tag;
use Tests\CafeTestCase;

class MenuBrowsingTest extends CafeTestCase
{
    public function test_the_home_page_lists_menus_grouped_by_category(): void
    {
        $coffee = Category::factory()->create(['name' => 'Coffee']);
        $snacks = Category::factory()->create(['name' => 'Snacks']);
        $this->makeMenu(['category_id' => $coffee->id, 'name' => 'Latte', 'price' => 25000]);
        $this->makeMenu(['category_id' => $snacks->id, 'name' => 'French Fries', 'price' => 18000]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeInOrder(['Coffee', 'Latte', 'Snacks', 'French Fries'])
            ->assertSee('Rp 25.000');
    }

    public function test_the_menu_can_be_filtered_by_tag(): void
    {
        $spicy = Tag::factory()->create(['name' => 'Spicy']);
        $hot = $this->makeMenu(['name' => 'Spicy Noodles']);
        $this->makeMenu(['name' => 'Plain Rice']);
        $hot->tags()->attach($spicy->id);

        $this->get(route('home', ['tag' => 'Spicy']))
            ->assertOk()
            ->assertSee('Spicy Noodles')
            ->assertDontSee('Plain Rice');
    }

    public function test_an_unavailable_menu_is_marked_as_sold_out_and_cannot_be_ordered(): void
    {
        $customer = $this->makeUser();
        $menu = $this->makeMenu(['name' => 'Seasonal Cake', 'is_available' => false]);
        $this->makeMenu(['name' => 'Americano', 'is_available' => true]);

        $this->actingAs($customer)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('Sold out')
            ->assertDontSee('name="items['.$menu->id.']"', false)
            ->assertSee('Place order');
    }

    public function test_a_guest_is_asked_to_log_in_to_order(): void
    {
        $this->makeMenu();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('to place an order')
            ->assertDontSee('Place order');
    }

    public function test_staff_can_see_the_menu_but_not_the_order_form(): void
    {
        $this->makeMenu();

        $this->actingAs($this->makeUser('cashier'))
            ->get(route('home'))
            ->assertOk()
            ->assertDontSee('Place order');
    }
}
