<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_can_be_rendered(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('LokalBites Dashboard');
    }

    public function test_admin_categories_page_can_be_rendered(): void
    {
        Category::create([
            'name' => 'Minuman Tradisional',
            'slug' => 'minuman-tradisional',
            'icon' => 'mug-hot',
            'description' => 'Aneka minuman khas',
        ]);

        $response = $this->get('/admin/categories');
        $response->assertStatus(200);
        $response->assertSee('Minuman Tradisional');
    }

    public function test_can_create_new_category(): void
    {
        $response = $this->post('/admin/categories', [
            'name' => 'Camilan Gurih',
            'slug' => 'camilan-gurih',
            'icon' => 'cookie',
            'description' => 'Aneka camilan',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'slug' => 'camilan-gurih',
        ]);
    }

    public function test_can_create_new_tag(): void
    {
        $response = $this->post('/admin/tags', [
            'name' => 'Favorit Pelanggan',
            'slug' => 'favorit-pelanggan',
            'color' => '#ff5722',
        ]);

        $response->assertRedirect(route('admin.tags.index'));
        $this->assertDatabaseHas('tags', [
            'slug' => 'favorit-pelanggan',
        ]);
    }

    public function test_admin_menus_page_can_be_rendered(): void
    {
        $response = $this->get('/admin/menus');
        $response->assertStatus(200);
        $response->assertSee('Menu Management Dashboard');
    }

    public function test_admin_orders_page_can_be_rendered(): void
    {
        $response = $this->get('/admin/orders');
        $response->assertStatus(200);
        $response->assertSee('Order Management Dashboard');
    }

    public function test_admin_reviews_page_can_be_rendered(): void
    {
        $response = $this->get('/admin/reviews');
        $response->assertStatus(200);
        $response->assertSee('Review Management Dashboard');
    }
}
