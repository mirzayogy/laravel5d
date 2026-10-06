<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ItemSpecification;
use App\Models\Profile;
use App\Models\Promotion;
use App\Models\Rental;
use App\Models\RentalDetail;
use App\Models\RentalItem;
use App\Models\RentalReturn;
use App\Models\Review;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseRelationsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 1: One-to-One User <-> Profile
     */
    public function test_user_has_one_profile_and_profile_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(Profile::class, $user->profile);
        $this->assertEquals($profile->id, $user->profile->id);

        $this->assertInstanceOf(User::class, $profile->user);
        $this->assertEquals($user->id, $profile->user->id);
    }

    /**
     * Test 2: One-to-One RentalItem <-> ItemSpecification
     */
    public function test_rental_item_has_one_specification_and_belongs_to_item(): void
    {
        $item = RentalItem::factory()->create();
        $spec = ItemSpecification::factory()->create(['rental_item_id' => $item->id]);

        $this->assertInstanceOf(ItemSpecification::class, $item->specification);
        $this->assertEquals($spec->id, $item->specification->id);

        $this->assertInstanceOf(RentalItem::class, $spec->rentalItem);
        $this->assertEquals($item->id, $spec->rentalItem->id);
    }

    /**
     * Test 3: One-to-One Rental <-> RentalReturn
     */
    public function test_rental_has_one_return_record_and_belongs_to_rental(): void
    {
        $rental = Rental::factory()->create();
        $return = RentalReturn::factory()->create(['rental_id' => $rental->id]);

        $this->assertInstanceOf(RentalReturn::class, $rental->returnRecord);
        $this->assertEquals($return->id, $rental->returnRecord->id);

        $this->assertInstanceOf(Rental::class, $return->rental);
        $this->assertEquals($rental->id, $return->rental->id);
    }

    /**
     * Test 4: One-to-Many Category -> RentalItem
     */
    public function test_category_has_many_rental_items_and_item_belongs_to_category(): void
    {
        $category = Category::factory()->create();
        $items = RentalItem::factory()->count(3)->create(['category_id' => $category->id]);

        $this->assertCount(3, $category->rentalItems);
        $this->assertTrue($category->rentalItems->contains($items->first()));

        $this->assertInstanceOf(Category::class, $items->first()->category);
        $this->assertEquals($category->id, $items->first()->category->id);
    }

    /**
     * Test 5: One-to-Many User -> Rental
     */
    public function test_user_has_many_rentals_and_rental_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $rentals = Rental::factory()->count(2)->create(['user_id' => $user->id]);

        $this->assertCount(2, $user->rentals);
        $this->assertTrue($user->rentals->contains($rentals->first()));

        $this->assertInstanceOf(User::class, $rentals->first()->user);
        $this->assertEquals($user->id, $rentals->first()->user->id);
    }

    /**
     * Test 6: One-to-Many User -> Review & RentalItem -> Review
     */
    public function test_user_and_rental_item_have_many_reviews(): void
    {
        $user = User::factory()->create();
        $item = RentalItem::factory()->create();
        $review = Review::factory()->create([
            'user_id' => $user->id,
            'rental_item_id' => $item->id,
        ]);

        $this->assertCount(1, $user->reviews);
        $this->assertCount(1, $item->reviews);

        $this->assertInstanceOf(User::class, $review->user);
        $this->assertInstanceOf(RentalItem::class, $review->rentalItem);
        $this->assertEquals($user->id, $review->user->id);
        $this->assertEquals($item->id, $review->rentalItem->id);
    }

    /**
     * Test 7: One-to-Many Rental -> RentalDetail & RentalItem -> RentalDetail
     */
    public function test_rental_and_item_have_many_rental_details(): void
    {
        $rental = Rental::factory()->create();
        $item = RentalItem::factory()->create();
        $detail = RentalDetail::factory()->create([
            'rental_id' => $rental->id,
            'rental_item_id' => $item->id,
        ]);

        $this->assertCount(1, $rental->details);
        $this->assertCount(1, $item->rentalDetails);

        $this->assertEquals($rental->id, $detail->rental->id);
        $this->assertEquals($item->id, $detail->rentalItem->id);
    }

    /**
     * Test 8: Many-to-Many RentalItem <-> Tag
     */
    public function test_rental_item_belongs_to_many_tags(): void
    {
        $item = RentalItem::factory()->create();
        $tags = Tag::factory()->count(3)->create();

        $item->tags()->attach($tags);

        $this->assertCount(3, $item->tags);
        $this->assertCount(1, $tags->first()->rentalItems);
        $this->assertTrue($tags->first()->rentalItems->contains($item));
    }

    /**
     * Test 9: Many-to-Many Rental <-> RentalItem with Pivot Data
     */
    public function test_rental_belongs_to_many_rental_items_with_pivot_data(): void
    {
        $rental = Rental::factory()->create();
        $item = RentalItem::factory()->create(['daily_rate' => 200000]);

        $rental->items()->attach($item->id, [
            'quantity' => 2,
            'price_per_day' => 200000,
            'subtotal' => 400000,
            'condition_before' => 'Sangat Baik',
        ]);

        $this->assertCount(1, $rental->items);
        $pivot = $rental->items->first()->pivot;

        $this->assertEquals(2, $pivot->quantity);
        $this->assertEquals(200000, $pivot->price_per_day);
        $this->assertEquals(400000, $pivot->subtotal);
        $this->assertEquals('Sangat Baik', $pivot->condition_before);
    }

    /**
     * Test 10: Many-to-Many User <-> Promotion with Pivot Data
     */
    public function test_user_belongs_to_many_promotions_with_pivot_data(): void
    {
        $user = User::factory()->create();
        $promo = Promotion::factory()->create();

        $user->promotions()->attach($promo->id, [
            'used_at' => now(),
            'discount_amount' => 50000.00,
        ]);

        $this->assertCount(1, $user->promotions);
        $pivot = $user->promotions->first()->pivot;

        $this->assertEquals(50000.00, $pivot->discount_amount);
        $this->assertNotNull($pivot->used_at);

        $this->assertCount(1, $promo->users);
        $this->assertTrue($promo->users->contains($user));
    }

    /**
     * Test 11: Has-Many-Through User -> RentalDetail through Rental
     */
    public function test_user_has_many_rental_details_through_rental(): void
    {
        $user = User::factory()->create();
        $rental = Rental::factory()->create(['user_id' => $user->id]);
        $details = RentalDetail::factory()->count(2)->create(['rental_id' => $rental->id]);

        $this->assertCount(2, $user->rentalDetails);
        $this->assertTrue($user->rentalDetails->contains($details->first()));
    }

    /**
     * Test 12: Has-Many-Through Category -> RentalDetail through RentalItem
     */
    public function test_category_has_many_rental_details_through_rental_item(): void
    {
        $category = Category::factory()->create();
        $item = RentalItem::factory()->create(['category_id' => $category->id]);
        $details = RentalDetail::factory()->count(3)->create(['rental_item_id' => $item->id]);

        $this->assertCount(3, $category->rentalDetails);
        $this->assertTrue($category->rentalDetails->contains($details->first()));
    }

    /**
     * Test 13: Has-Many-Through Category -> Review through RentalItem
     */
    public function test_category_has_many_reviews_through_rental_item(): void
    {
        $category = Category::factory()->create();
        $item = RentalItem::factory()->create(['category_id' => $category->id]);
        $reviews = Review::factory()->count(2)->create(['rental_item_id' => $item->id]);

        $this->assertCount(2, $category->reviews);
        $this->assertTrue($category->reviews->contains($reviews->first()));
    }
}
