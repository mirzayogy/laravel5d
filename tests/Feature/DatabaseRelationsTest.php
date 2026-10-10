<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\BookDetail;
use App\Models\Profile;
use App\Models\Promotion;
use App\Models\Loan;
use App\Models\LoanDetail;
use App\Models\Book;
use App\Models\LoanReturn;
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
     * Test 2: One-to-One Book <-> BookDetail
     */
    public function test_loan_item_has_one_specification_and_belongs_to_item(): void
    {
        $item = Book::factory()->create();
        $spec = BookDetail::factory()->create(['book_id' => $item->id]);

        $this->assertInstanceOf(BookDetail::class, $item->specification);
        $this->assertEquals($spec->id, $item->specification->id);

        $this->assertInstanceOf(Book::class, $spec->book);
        $this->assertEquals($item->id, $spec->book->id);
    }

    /**
     * Test 3: One-to-One Loan <-> LoanReturn
     */
    public function test_loan_has_one_return_record_and_belongs_to_loan(): void
    {
        $loan = Loan::factory()->create();
        $return = LoanReturn::factory()->create(['loan_id' => $loan->id]);

        $this->assertInstanceOf(LoanReturn::class, $loan->returnRecord);
        $this->assertEquals($return->id, $loan->returnRecord->id);

        $this->assertInstanceOf(Loan::class, $return->loan);
        $this->assertEquals($loan->id, $return->loan->id);
    }

    /**
     * Test 4: One-to-Many Category -> Book
     */
    public function test_category_has_many_books_and_item_belongs_to_category(): void
    {
        $category = Category::factory()->create();
        $books = Book::factory()->count(3)->create(['category_id' => $category->id]);

        $this->assertCount(3, $category->books);
        $this->assertTrue($category->books->contains($books->first()));

        $this->assertInstanceOf(Category::class, $books->first()->category);
        $this->assertEquals($category->id, $books->first()->category->id);
    }

    /**
     * Test 5: One-to-Many User -> Loan
     */
    public function test_user_has_many_loans_and_loan_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $loans = Loan::factory()->count(2)->create(['user_id' => $user->id]);

        $this->assertCount(2, $user->loans);
        $this->assertTrue($user->loans->contains($loans->first()));

        $this->assertInstanceOf(User::class, $loans->first()->user);
        $this->assertEquals($user->id, $loans->first()->user->id);
    }

    /**
     * Test 6: One-to-Many User -> Review & Book -> Review
     */
    public function test_user_and_loan_item_have_many_reviews(): void
    {
        $user = User::factory()->create();
        $item = Book::factory()->create();
        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $item->id,
        ]);

        $this->assertCount(1, $user->reviews);
        $this->assertCount(1, $item->reviews);

        $this->assertInstanceOf(User::class, $review->user);
        $this->assertInstanceOf(Book::class, $review->book);
        $this->assertEquals($user->id, $review->user->id);
        $this->assertEquals($item->id, $review->book->id);
    }

    /**
     * Test 7: One-to-Many Loan -> LoanDetail & Book -> LoanDetail
     */
    public function test_loan_and_item_have_many_loan_details(): void
    {
        $loan = Loan::factory()->create();
        $item = Book::factory()->create();
        $detail = LoanDetail::factory()->create([
            'loan_id' => $loan->id,
            'book_id' => $item->id,
        ]);

        $this->assertCount(1, $loan->details);
        $this->assertCount(1, $item->loanDetails);

        $this->assertEquals($loan->id, $detail->loan->id);
        $this->assertEquals($item->id, $detail->book->id);
    }

    /**
     * Test 8: Many-to-Many Book <-> Tag
     */
    public function test_loan_item_belongs_to_many_tags(): void
    {
        $item = Book::factory()->create();
        $tags = Tag::factory()->count(3)->create();

        $item->tags()->attach($tags);

        $this->assertCount(3, $item->tags);
        $this->assertCount(1, $tags->first()->books);
        $this->assertTrue($tags->first()->books->contains($item));
    }

    /**
     * Test 9: Many-to-Many Loan <-> Book with Pivot Data
     */
    public function test_loan_belongs_to_many_books_with_pivot_data(): void
    {
        $loan = Loan::factory()->create();
        $item = Book::factory()->create(['borrow_fee' => 200000]);

        $loan->books()->attach($item->id, [
            'quantity' => 2,
            'fee_per_day' => 200000,
            'subtotal' => 400000,
            'condition_out' => 'Good',
        ]);

        $this->assertCount(1, $loan->books);
        $pivot = $loan->books->first()->pivot;

        $this->assertEquals(2, $pivot->quantity);
        $this->assertEquals(200000, $pivot->fee_per_day);
        $this->assertEquals(400000, $pivot->subtotal);
        $this->assertEquals('Good', $pivot->condition_out);
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
     * Test 11: Has-Many-Through User -> LoanDetail through Loan
     */
    public function test_user_has_many_loan_details_through_loan(): void
    {
        $user = User::factory()->create();
        $loan = Loan::factory()->create(['user_id' => $user->id]);
        $details = LoanDetail::factory()->count(2)->create(['loan_id' => $loan->id]);

        $this->assertCount(2, $user->loanDetails);
        $this->assertTrue($user->loanDetails->contains($details->first()));
    }

    /**
     * Test 12: Has-Many-Through Category -> LoanDetail through Book
     */
    public function test_category_has_many_loan_details_through_loan_item(): void
    {
        $category = Category::factory()->create();
        $item = Book::factory()->create(['category_id' => $category->id]);
        $details = LoanDetail::factory()->count(3)->create(['book_id' => $item->id]);

        $this->assertCount(3, $category->loanDetails);
        $this->assertTrue($category->loanDetails->contains($details->first()));
    }

    /**
     * Test 13: Has-Many-Through Category -> Review through Book
     */
    public function test_category_has_many_reviews_through_loan_item(): void
    {
        $category = Category::factory()->create();
        $item = Book::factory()->create(['category_id' => $category->id]);
        $reviews = Review::factory()->count(2)->create(['book_id' => $item->id]);

        $this->assertCount(2, $category->reviews);
        $this->assertTrue($category->reviews->contains($reviews->first()));
    }
}
