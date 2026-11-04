<?php

namespace App\Support;

use App\Models\Habit;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Random\Randomizer;

/**
 * The line that welcomes you on the dashboard, in the spirit of "Good evening,
 * Ayu. Ready to hit the gym?". It is random on every visit, but only says
 * things that fit: the habits still open today, the time of day, your streak.
 */
class Greeting
{
    /** What to ask for each category slug that still has an open habit today. */
    public const CATEGORY_LINES = [
        'health-fitness' => ['Ready to hit the gym?', 'Ready to move your body?', 'Time for a workout?', 'Ready to break a sweat?'],
        'mindfulness' => ['Ready for a few calm minutes?', 'Ready to take a deep breath?', 'Ready to clear your mind?'],
        'productivity' => ['Ready to get things done?', 'Ready for some deep work?', 'Ready to tackle your list?'],
        'better-sleep' => ['Ready to wind down for the night?', 'Ready to put the screens away?'],
        'stay-hydrated' => ['Ready for a glass of water?', 'Time to drink some water?', 'Ready to hydrate?'],
        'read-more' => ['Ready to read a few pages?', 'Ready to open a book?', 'Ready to get lost in a story?'],
        'social-connections' => ['Ready to message a friend?', 'Ready to reach out to someone?', 'Ready to catch up with someone?'],
        'self-care' => ['Ready for a little self-care?', 'Ready to look after yourself?', 'Ready for some you-time?'],
    ];

    /** Bedtime habits are only suggested in the evening and at night. */
    private const EVENING_ONLY = ['better-sleep'];

    public const DONE_LINES = ['Everything is done today. Nice work!', 'All habits checked off. You earned a break.', 'A perfect day so far. Enjoy the rest of it.'];

    public const EMPTY_LINES = ['Ready to add your first habit?', 'Ready to start something small?'];

    /**
     * @param  Collection<int, array{habit: Habit, done: bool}>  $rows  today's active habits
     * @param  array{current: int, best: int}  $streak
     * @return array{hello: string, line: string}
     */
    public static function for(User $user, CarbonImmutable $now, Collection $rows, array $streak, ?Randomizer $rng = null): array
    {
        $rng ??= new Randomizer;
        $pick = fn (array $items) => __($items[$rng->getInt(0, count($items) - 1)]);

        $hour = $now->hour;
        $part = match (true) {
            $hour >= 5 && $hour < 12 => __('Good morning'),
            $hour >= 12 && $hour < 18 => __('Good afternoon'),
            default => __('Good evening'),
        };
        $first = explode(' ', trim($user->name))[0];
        $hello = ($rng->getInt(0, 4) < 2 ? $part : $pick(['Hello', 'Hi', 'Welcome back'])).', '.$first;

        $open = $rows->where('done', false);
        $done = $rows->where('done', true)->count();
        $evening = $hour >= 18 || $hour < 5;

        $candidates = [];

        foreach ($open->map(fn ($r) => $r['habit']->category->slug)->unique() as $slug) {
            if (isset(self::CATEGORY_LINES[$slug]) && (! in_array($slug, self::EVENING_ONLY, true) || $evening)) {
                $candidates[] = $pick(self::CATEGORY_LINES[$slug]);
            }
        }
        if ($open->isNotEmpty() && $streak['current'] >= 3) {
            $candidates[] = __('Keep your :n-day streak alive?', ['n' => $streak['current']]);
        }
        if ($open->isNotEmpty() && $done > 0) {
            $candidates[] = __(':done of :total done. Ready for the next one?', ['done' => $done, 'total' => $rows->count()]);
        }

        $line = match (true) {
            $rows->isEmpty() => $pick(self::EMPTY_LINES),
            $open->isEmpty() => $pick(self::DONE_LINES),
            $candidates !== [] => $pick($candidates),
            default => __('Ready for today?'), // only habits we have no line for
        };

        return ['hello' => $hello, 'line' => $line];
    }
}
