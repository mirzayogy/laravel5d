<?php

namespace Tests\Feature;

use App\Enums\CyclePhase;
use App\Models\Cycle;
use App\Models\Profile;
use App\Models\User;
use App\Services\CyclePredictionService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CyclePredictionServiceTest extends TestCase
{
    use RefreshDatabase;

    private CyclePredictionService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new CyclePredictionService;
    }

    public function test_no_prediction_without_any_cycle(): void
    {
        $user = User::factory()->create();

        $this->assertNull($this->service->predictNextPeriodStart($user));
        $this->assertNull($this->service->predictFertileWindow($user));
        $this->assertNull($this->service->phaseOn($user, CarbonImmutable::parse('2026-10-10')));
    }

    public function test_average_falls_back_to_default_then_profile(): void
    {
        $user = User::factory()->create();
        $this->assertSame(28, $this->service->averageCycleLength($user));
        $this->assertSame(5, $this->service->averagePeriodLength($user));

        Profile::factory()->for($user)->create(['avg_cycle_length' => 31, 'avg_period_length' => 6]);
        $user->refresh();

        $this->assertSame(31, $this->service->averageCycleLength($user));
        $this->assertSame(6, $this->service->averagePeriodLength($user));
    }

    public function test_average_uses_recorded_cycle_lengths(): void
    {
        $user = User::factory()->create();
        Cycle::factory()->for($user)->create(['start_date' => '2026-07-01', 'cycle_length' => 26, 'period_length' => 4]);
        Cycle::factory()->for($user)->create(['start_date' => '2026-07-27', 'cycle_length' => 30, 'period_length' => 6]);
        Cycle::factory()->for($user)->create(['start_date' => '2026-08-26']);

        $this->assertSame(28, $this->service->averageCycleLength($user));
        $this->assertSame(5, $this->service->averagePeriodLength($user));
    }

    public function test_predicts_next_period_ovulation_and_fertile_window(): void
    {
        $user = User::factory()->create();
        Cycle::factory()->for($user)->create(['start_date' => '2026-10-01']);

        $this->assertSame('2026-10-29', $this->service->predictNextPeriodStart($user)->toDateString());
        $this->assertSame('2026-10-15', $this->service->predictOvulationDate($user)->toDateString());

        $window = $this->service->predictFertileWindow($user);
        $this->assertSame('2026-10-10', $window['start']->toDateString());
        $this->assertSame('2026-10-16', $window['end']->toDateString());
    }

    public function test_record_period_start_closes_previous_cycle(): void
    {
        $user = User::factory()->create();
        $first = $this->service->recordPeriodStart($user, CarbonImmutable::parse('2026-09-01'));
        $this->service->recordPeriodStart($user, CarbonImmutable::parse('2026-09-30'));

        $first->refresh();
        $this->assertSame(29, $first->cycle_length);
        $this->assertSame('2026-09-29', $first->end_date->toDateString());
        $this->assertSame(2, $user->cycles()->count());
    }

    public function test_phase_for_each_part_of_a_default_cycle(): void
    {
        $user = User::factory()->create();
        Cycle::factory()->for($user)->create(['start_date' => '2026-10-01']);

        $phases = [
            '2026-10-01' => CyclePhase::Menstruation,
            '2026-10-05' => CyclePhase::Menstruation,
            '2026-10-06' => CyclePhase::Follicular,
            '2026-10-13' => CyclePhase::Follicular,
            '2026-10-14' => CyclePhase::Ovulation,
            '2026-10-16' => CyclePhase::Ovulation,
            '2026-10-17' => CyclePhase::Luteal,
            '2026-10-28' => CyclePhase::Luteal,
        ];

        foreach ($phases as $date => $expected) {
            $this->assertSame($expected, $this->service->phaseOn($user, CarbonImmutable::parse($date)), $date);
        }
    }

    public function test_irregular_cycle_detection(): void
    {
        $this->assertTrue($this->service->isIrregular(20));
        $this->assertFalse($this->service->isIrregular(21));
        $this->assertFalse($this->service->isIrregular(35));
        $this->assertTrue($this->service->isIrregular(36));
    }
}
