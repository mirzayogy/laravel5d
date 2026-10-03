<?php

namespace Tests\Feature;

use App\Models\Symptom;
use Database\Seeders\SymptomSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SymptomSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_is_idempotent(): void
    {
        $this->seed(SymptomSeeder::class);
        $this->seed(SymptomSeeder::class);

        $this->assertSame(10, Symptom::count());
        $this->assertDatabaseHas('symptoms', ['slug' => 'kram-perut', 'category' => 'physical']);
    }
}
