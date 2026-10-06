<?php

namespace Tests\Feature;

use App\Models\InspectionReport;
use App\Models\Invoice;
use App\Models\Mechanic;
use App\Models\Promotion;
use App\Models\ServiceBooking;
use App\Models\ServiceCategory;
use App\Models\ServicePackage;
use App\Models\ServiceReview;
use App\Models\SparePart;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseRelationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_user_has_one_user_profile(): void
    {
        $user = User::where('email', 'khairil@example.com')->first();
        $this->assertNotNull($user);
        $this->assertInstanceOf(UserProfile::class, $user->profile);
        $this->assertEquals('Banjarbaru', $user->profile->city);
        $this->assertEquals($user->id, $user->profile->user->id);
    }

    public function test_user_has_many_vehicles(): void
    {
        $user = User::where('email', 'khairil@example.com')->first();
        $this->assertCount(2, $user->vehicles);
        $this->assertEquals('Toyota', $user->vehicles->first()->brand);
        $this->assertEquals($user->id, $user->vehicles->first()->user->id);
    }

    public function test_vehicle_has_many_service_bookings(): void
    {
        $vehicle = Vehicle::where('plate_number', 'DA 1205 KI')->first();
        $this->assertNotNull($vehicle);
        $this->assertGreaterThanOrEqual(1, $vehicle->serviceBookings->count());
        $this->assertEquals($vehicle->id, $vehicle->serviceBookings->first()->vehicle->id);
    }

    public function test_service_category_has_many_service_packages(): void
    {
        $category = ServiceCategory::where('slug', 'perawatan-berkala')->first();
        $this->assertNotNull($category);
        $this->assertCount(2, $category->servicePackages);
        $this->assertEquals($category->id, $category->servicePackages->first()->serviceCategory->id);
    }

    public function test_mechanic_has_many_service_bookings(): void
    {
        $mechanic = Mechanic::first();
        $this->assertNotNull($mechanic);
        $this->assertGreaterThanOrEqual(1, $mechanic->serviceBookings->count());
        $this->assertEquals($mechanic->id, $mechanic->serviceBookings->first()->mechanic->id);
    }

    public function test_service_booking_has_one_inspection_report(): void
    {
        $booking = ServiceBooking::where('booking_code', 'SB-202610-001')->first();
        $this->assertNotNull($booking);
        $this->assertInstanceOf(InspectionReport::class, $booking->inspectionReport);
        $this->assertEquals(45200, $booking->inspectionReport->odometer_km);
        $this->assertEquals($booking->id, $booking->inspectionReport->serviceBooking->id);
    }

    public function test_service_booking_has_one_invoice(): void
    {
        $booking = ServiceBooking::where('booking_code', 'SB-202610-001')->first();
        $this->assertNotNull($booking);
        $this->assertInstanceOf(Invoice::class, $booking->invoice);
        $this->assertEquals('Paid', $booking->invoice->payment_status);
        $this->assertEquals($booking->id, $booking->invoice->serviceBooking->id);
    }

    public function test_service_booking_has_many_reviews(): void
    {
        $booking = ServiceBooking::where('booking_code', 'SB-202610-001')->first();
        $this->assertNotNull($booking);
        $this->assertCount(1, $booking->reviews);
        $this->assertEquals(5, $booking->reviews->first()->rating);
        $this->assertEquals($booking->id, $booking->reviews->first()->serviceBooking->id);
    }

    public function test_service_booking_belongs_to_many_service_packages_with_pivot(): void
    {
        $booking = ServiceBooking::where('booking_code', 'SB-202610-001')->first();
        $this->assertNotNull($booking);
        $this->assertCount(2, $booking->servicePackages);

        $firstPackage = $booking->servicePackages->first();
        $this->assertNotNull($firstPackage->pivot->package_price);
        $this->assertNotNull($firstPackage->pivot->technician_notes);
    }

    public function test_service_booking_belongs_to_many_spare_parts_with_pivot(): void
    {
        $booking = ServiceBooking::where('booking_code', 'SB-202610-001')->first();
        $this->assertNotNull($booking);
        $this->assertCount(2, $booking->spareParts);

        $firstPart = $booking->spareParts->first();
        $this->assertNotNull($firstPart->pivot->quantity);
        $this->assertNotNull($firstPart->pivot->unit_price);
        $this->assertNotNull($firstPart->pivot->subtotal_price);
    }

    public function test_user_belongs_to_many_promotions_with_pivot(): void
    {
        $user = User::where('email', 'khairil@example.com')->first();
        $this->assertNotNull($user);
        $this->assertCount(1, $user->promotions);

        $firstPromo = $user->promotions->first();
        $this->assertEquals(50000, $firstPromo->pivot->discount_applied);
        $this->assertNotNull($firstPromo->pivot->used_at);
    }

    public function test_user_has_many_service_bookings_through_vehicles(): void
    {
        $user = User::where('email', 'khairil@example.com')->first();
        $this->assertNotNull($user);
        $bookings = $user->serviceBookings;
        $this->assertCount(2, $bookings);
        $this->assertTrue($bookings->contains('booking_code', 'SB-202610-001'));
        $this->assertTrue($bookings->contains('booking_code', 'SB-202610-002'));
    }
}
