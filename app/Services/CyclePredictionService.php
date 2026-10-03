<?php

namespace App\Services;

use App\Enums\CyclePhase;
use App\Models\Cycle;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

class CyclePredictionService
{
    public const int DEFAULT_CYCLE_LENGTH = 28;

    public const int DEFAULT_PERIOD_LENGTH = 5;

    public const int LUTEAL_PHASE_LENGTH = 14;

    public const int MIN_REGULAR_CYCLE_LENGTH = 21;

    public const int MAX_REGULAR_CYCLE_LENGTH = 35;

    public const int CYCLES_TO_AVERAGE = 6;

    /**
     * Start a new cycle and close the previous one, storing its measured length.
     */
    public function recordPeriodStart(User $user, CarbonInterface $startDate): Cycle
    {
        $startDate = CarbonImmutable::parse($startDate)->startOfDay();

        $previous = $user->cycles()
            ->whereDate('start_date', '<', $startDate)
            ->latest('start_date')
            ->first();

        if ($previous !== null) {
            $previous->update([
                'end_date' => $startDate->subDay(),
                'cycle_length' => $previous->start_date->diffInDays($startDate),
            ]);
        }

        return $user->cycles()->create(['start_date' => $startDate]);
    }

    /**
     * Average of the most recent completed cycle lengths, falling back to the profile and then the default.
     */
    public function averageCycleLength(User $user): int
    {
        $average = $user->cycles()
            ->whereNotNull('cycle_length')
            ->latest('start_date')
            ->limit(self::CYCLES_TO_AVERAGE)
            ->avg('cycle_length');

        return (int) round($average ?? $user->profile?->avg_cycle_length ?? self::DEFAULT_CYCLE_LENGTH);
    }

    /**
     * Average of the most recent recorded period lengths, falling back to the profile and then the default.
     */
    public function averagePeriodLength(User $user): int
    {
        $average = $user->cycles()
            ->whereNotNull('period_length')
            ->latest('start_date')
            ->limit(self::CYCLES_TO_AVERAGE)
            ->avg('period_length');

        return (int) round($average ?? $user->profile?->avg_period_length ?? self::DEFAULT_PERIOD_LENGTH);
    }

    public function predictNextPeriodStart(User $user): ?CarbonImmutable
    {
        $latest = $this->latestCycle($user);

        if ($latest === null) {
            return null;
        }

        return CarbonImmutable::parse($latest->start_date)->addDays($this->averageCycleLength($user));
    }

    public function predictOvulationDate(User $user): ?CarbonImmutable
    {
        return $this->predictNextPeriodStart($user)?->subDays(self::LUTEAL_PHASE_LENGTH);
    }

    /**
     * The fertile window runs from five days before ovulation until one day after.
     *
     * @return array{start: CarbonImmutable, end: CarbonImmutable}|null
     */
    public function predictFertileWindow(User $user): ?array
    {
        $ovulation = $this->predictOvulationDate($user);

        if ($ovulation === null) {
            return null;
        }

        return [
            'start' => $ovulation->subDays(5),
            'end' => $ovulation->addDay(),
        ];
    }

    /**
     * The phase of the cycle the given date falls into, based on the most recent cycle start on or before it.
     */
    public function phaseOn(User $user, CarbonInterface $date): ?CyclePhase
    {
        $date = CarbonImmutable::parse($date)->startOfDay();

        $cycle = $user->cycles()
            ->whereDate('start_date', '<=', $date)
            ->latest('start_date')
            ->first();

        if ($cycle === null) {
            return null;
        }

        $dayOfCycle = $cycle->start_date->diffInDays($date) + 1;
        $ovulationDay = $this->averageCycleLength($user) - self::LUTEAL_PHASE_LENGTH + 1;

        return match (true) {
            $dayOfCycle <= $this->averagePeriodLength($user) => CyclePhase::Menstruation,
            $dayOfCycle < $ovulationDay - 1 => CyclePhase::Follicular,
            $dayOfCycle <= $ovulationDay + 1 => CyclePhase::Ovulation,
            default => CyclePhase::Luteal,
        };
    }

    public function isIrregular(int $cycleLength): bool
    {
        return $cycleLength < self::MIN_REGULAR_CYCLE_LENGTH
            || $cycleLength > self::MAX_REGULAR_CYCLE_LENGTH;
    }

    private function latestCycle(User $user): ?Cycle
    {
        return $user->cycles()->latest('start_date')->first();
    }
}
