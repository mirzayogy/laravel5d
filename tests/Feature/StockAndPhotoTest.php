<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Order;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\CafeTestCase;

class StockAndPhotoTest extends CafeTestCase
{
    private const PNG = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';

    private function png(string $name = 'menu.png'): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($name, base64_decode(self::PNG));
    }

    public function test_ordering_reduces_the_stock(): void
    {
        $menu = $this->makeMenu(['stock' => 10]);

        $this->actingAs($this->makeUser('customer'))->post(route('orders.store'), ['items' => [$menu->id => 3]]);

        $this->assertSame(7, $menu->fresh()->stock);
    }

    public function test_cannot_order_more_than_the_stock(): void
    {
        $menu = $this->makeMenu(['stock' => 2]);

        $this->actingAs($this->makeUser('customer'))->post(route('orders.store'), ['items' => [$menu->id => 3]])
            ->assertSessionHasErrors('items');

        $this->assertSame(2, $menu->fresh()->stock);
        $this->assertSame(0, Order::count());
    }

    public function test_unlimited_stock_stays_unlimited(): void
    {
        $menu = $this->makeMenu(['stock' => null]);

        $this->actingAs($this->makeUser('customer'))->post(route('orders.store'), ['items' => [$menu->id => 5]]);

        $this->assertNull($menu->fresh()->stock);
    }

    public function test_cancelling_an_order_gives_the_stock_back(): void
    {
        $customer = $this->makeUser('customer');
        $menu = $this->makeMenu(['stock' => 10]);
        $this->actingAs($customer)->post(route('orders.store'), ['items' => [$menu->id => 4]]);
        $order = Order::firstOrFail();

        $this->actingAs($customer)->post(route('orders.cancel', $order));

        $this->assertSame(10, $menu->fresh()->stock);
        $this->assertSame('cancelled', $order->fresh()->status);
    }

    public function test_cashier_cancelling_a_paid_order_gives_stock_back_and_refunds(): void
    {
        $customer = $this->makeUser('customer');
        $menu = $this->makeMenu(['stock' => 5]);
        $this->actingAs($customer)->post(route('orders.store'), ['items' => [$menu->id => 2]]);
        $order = Order::firstOrFail();
        $order->update(['payment_status' => 'paid']);

        $this->actingAs($this->makeUser('cashier'))->patch(route('staff.orders.status', $order), ['status' => 'cancelled']);

        $this->assertSame(5, $menu->fresh()->stock);
        $this->assertSame('refunded', $order->fresh()->payment_status);
    }

    public function test_menu_with_zero_stock_is_sold_out_on_the_menu_page(): void
    {
        $menu = $this->makeMenu(['stock' => 0, 'is_available' => true]);

        $this->actingAs($this->makeUser('customer'))->get(route('home'))
            ->assertSee('Sold out')
            ->assertDontSee('name="items['.$menu->id.']"', false);
    }

    public function test_low_stock_menu_shows_a_warning(): void
    {
        $this->makeMenu(['stock' => 3]);

        $this->get(route('home'))->assertSee('Only')->assertSee('left');
    }

    public function test_admin_can_create_a_menu_with_photo_and_stock(): void
    {
        Storage::fake('public');
        $category = \App\Models\Category::factory()->create();

        $this->actingAs($this->makeUser('admin'))->post(route('admin.menus.store'), [
            'category_id' => $category->id,
            'name' => 'Kopi Susu',
            'price' => 20000,
            'stock' => 12,
            'is_available' => 1,
            'image' => $this->png(),
        ])->assertRedirect(route('admin.menus.index'));

        $menu = Menu::where('name', 'Kopi Susu')->firstOrFail();
        $this->assertSame(12, $menu->stock);
        Storage::disk('public')->assertExists($menu->image);
        $this->assertNotNull($menu->image_url);
    }

    public function test_replacing_and_removing_the_photo_deletes_the_old_file(): void
    {
        Storage::fake('public');
        $admin = $this->makeUser('admin');
        $menu = $this->makeMenu();
        $menu->update(['image' => $this->png('old.png')->store('menus', 'public')]);
        $old = $menu->image;

        $payload = ['category_id' => $menu->category_id, 'name' => $menu->name, 'price' => $menu->price, 'is_available' => 1];

        $this->actingAs($admin)->put(route('admin.menus.update', $menu), $payload + ['image' => $this->png('new.png')]);
        Storage::disk('public')->assertMissing($old);
        $new = $menu->fresh()->image;
        Storage::disk('public')->assertExists($new);

        $this->actingAs($admin)->put(route('admin.menus.update', $menu), $payload + ['remove_image' => 1]);
        $this->assertNull($menu->fresh()->image);
        Storage::disk('public')->assertMissing($new);
    }

    public function test_photo_must_be_an_image(): void
    {
        Storage::fake('public');
        $category = \App\Models\Category::factory()->create();

        $this->actingAs($this->makeUser('admin'))->post(route('admin.menus.store'), [
            'category_id' => $category->id,
            'name' => 'Bad',
            'price' => 1000,
            'image' => UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf'),
        ])->assertSessionHasErrors('image');
    }

    public function test_menu_without_photo_shows_a_placeholder_and_photo_menu_shows_the_image(): void
    {
        $this->makeMenu(['name' => 'No Photo Menu']);
        $this->makeMenu(['name' => 'Shipped Photo Menu', 'image' => 'images/menu/shipped.jpg']);

        $this->get(route('home'))
            ->assertSee('role="img"', false)
            ->assertSee('images/menu/shipped.jpg', false);
    }

    public function test_admin_menu_list_filters_on_the_server(): void
    {
        $admin = $this->makeUser('admin');
        $this->makeMenu(['name' => 'Kopi Tubruk']);
        $this->makeMenu(['name' => 'Es Teh Manis']);
        $this->makeMenu(['name' => 'Habis Menu', 'is_available' => false]);

        $this->actingAs($admin)->get(route('admin.menus.index', ['q' => 'Tubruk']))
            ->assertSee('Kopi Tubruk')->assertDontSee('Es Teh Manis');

        $this->actingAs($admin)->get(route('admin.menus.index', ['availability' => 'sold_out']))
            ->assertSee('Habis Menu')->assertDontSee('Kopi Tubruk');
    }

    public function test_menu_used_in_orders_cannot_be_deleted(): void
    {
        $menu = $this->makeMenu();
        $this->makeOrder($this->makeUser('customer'), [$menu]);

        $this->actingAs($this->makeUser('admin'))->delete(route('admin.menus.destroy', $menu))->assertSessionHas('error');

        $this->assertModelExists($menu);
    }

    public function test_link_photos_command_attaches_files_named_after_the_menu(): void
    {
        $menu = $this->makeMenu(['name' => 'Foto Test Menu']);
        $path = public_path('images/menu/foto-test-menu.jpg');
        @mkdir(dirname($path), 0777, true);
        file_put_contents($path, base64_decode(self::PNG));

        try {
            $this->artisan('menu:link-photos')->assertSuccessful();
            $this->assertSame('images/menu/foto-test-menu.jpg', $menu->fresh()->image);
        } finally {
            @unlink($path);
        }
    }
}
