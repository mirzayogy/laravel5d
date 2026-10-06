<?php

namespace Tests\Feature;

use App\Models\Badge;
use App\Models\Category;
use App\Models\CustomerReview;
use App\Models\Fish;
use App\Models\Profile;
use App\Models\RestockReminder;
use App\Models\SalesLog;
use App\Models\SalesReport;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseRelationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_one_profile_and_profile_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->profile->is($profile));
        $this->assertTrue($profile->user->is($user));
    }

    public function test_user_has_many_fishes_and_fish_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $fish = Fish::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $this->assertTrue($user->fishes->contains($fish));
        $this->assertTrue($fish->user->is($user));
    }

    public function test_category_has_many_fishes_and_fish_belongs_to_category(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $fish = Fish::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $this->assertTrue($category->fishes->contains($fish));
        $this->assertTrue($fish->category->is($category));
    }

    public function test_fish_has_many_sales_logs_and_sales_log_belongs_to_fish(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $fish = Fish::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);
        $log = SalesLog::factory()->create(['fish_id' => $fish->id]);

        $this->assertTrue($fish->logs->contains($log));
        $this->assertTrue($log->fish->is($fish));
    }

    public function test_user_has_many_through_sales_logs(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $fish = Fish::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);
        $log = SalesLog::factory()->create(['fish_id' => $fish->id]);

        $this->assertTrue($user->salesLogs->contains($log));
    }

    public function test_category_has_many_through_sales_logs(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $fish = Fish::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);
        $log = SalesLog::factory()->create(['fish_id' => $fish->id]);

        $this->assertTrue($category->salesLogs->contains($log));
    }

    public function test_fish_has_many_restock_reminders_and_reminder_belongs_to_fish(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $fish = Fish::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);
        $reminder = RestockReminder::factory()->create(['fish_id' => $fish->id]);

        $this->assertTrue($fish->restockReminders->contains($reminder));
        $this->assertTrue($reminder->fish->is($fish));
    }

    public function test_fish_belongs_to_many_tags_and_tag_belongs_to_many_fishes(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $fish = Fish::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);
        $tag = Tag::factory()->create();

        $fish->tags()->attach($tag->id);

        $this->assertTrue($fish->tags->contains($tag));
        $this->assertTrue($tag->fishes->contains($fish));
    }

    public function test_user_has_many_customer_reviews_and_customer_review_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $review = CustomerReview::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->customerReviews->contains($review));
        $this->assertTrue($review->user->is($user));
    }

    public function test_user_has_many_sales_reports_and_sales_report_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $report = SalesReport::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->salesReports->contains($report));
        $this->assertTrue($report->user->is($user));
    }

    public function test_customer_review_has_one_sales_report_and_sales_report_belongs_to_customer_review(): void
    {
        $user = User::factory()->create();
        $review = CustomerReview::factory()->create(['user_id' => $user->id]);
        $report = SalesReport::factory()->create([
            'user_id' => $user->id,
            'customer_review_id' => $review->id,
        ]);

        $this->assertTrue($review->salesReport->is($report));
        $this->assertTrue($report->customerReview->is($review));
    }

    public function test_user_belongs_to_many_badges_with_pivot(): void
    {
        $user = User::factory()->create();
        $badge = Badge::factory()->create();

        $user->badges()->attach($badge->id, ['earned_at' => now()]);

        $this->assertTrue($user->badges->contains($badge));
        $this->assertNotNull($user->badges->first()->pivot->earned_at);
        $this->assertTrue($badge->users->contains($user));
    }
}
