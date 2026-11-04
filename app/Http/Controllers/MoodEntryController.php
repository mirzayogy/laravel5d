<?php

namespace App\Http\Controllers;

use App\Models\MoodEntry;
use App\Services\AchievementService;
use Illuminate\Http\Request;

class MoodEntryController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $today = $user->today();

        return view('mood.index', [
            'today' => $today,
            'todayMood' => $user->moodEntries()->whereDate('entry_date', $today->toDateString())->first(),
            'entries' => $user->moodEntries()->with('journalEntry')->orderByDesc('entry_date')->limit(365)->get(),
        ]);
    }

    /**
     * One mood per user per day, so saving again for the same date updates it.
     */
    public function store(Request $request, AchievementService $achievements)
    {
        $user = $request->user();

        $data = $request->validate([
            'entry_date' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:'.$user->today()->toDateString()],
            'mood_level' => ['required', 'integer', 'between:1,5'],
            'note' => ['nullable', 'string', 'max:255'],
        ], [
            'mood_level.required' => __('Pick how you feel first.'),
        ]);

        $date = $data['entry_date'] ?? $user->today()->toDateString();

        $mood = $user->moodEntries()->updateOrCreate(
            ['entry_date' => $date],
            ['mood_level' => $data['mood_level'], 'note' => $data['note'] ?? null],
        );

        // Attach a journal written the same day that is not linked to a mood yet.
        if (! $mood->journalEntry()->exists()) {
            $user->journalEntries()->whereDate('entry_date', $date)->whereNull('mood_entry_id')->first()
                ?->update(['mood_entry_id' => $mood->id]);
        }

        $earned = $achievements->award($user);

        $journalUrl = route('journal.create', ['mood' => $mood->mood_level, 'date' => $date]);

        if ($request->expectsJson()) {
            return response()->json([
                'mood_level' => $mood->mood_level,
                'label' => __(config('habitude.moods')[$mood->mood_level]),
                'journal_url' => $journalUrl,
                'has_journal' => $mood->journalEntry()->exists(),
                'earned' => $earned->map(fn ($a) => ['name' => $a->display_name, 'description' => $a->display_description])->all(),
            ]);
        }

        $redirect = redirect()->route('mood.index')->with('success', __('Mood saved.'));

        // Close the loop: offer to write about it, unless a journal already exists for that day
        return $mood->journalEntry()->exists() ? $redirect : $redirect->with('action_url', $journalUrl)->with('action_label', 'Write about it');
    }

    public function destroy(Request $request, MoodEntry $mood)
    {
        abort_unless($mood->user_id === $request->user()->id, 404);

        $mood->delete(); // journal_entries.mood_entry_id is set to null by the foreign key

        return redirect()->route('mood.index')->with('success', __('Mood entry deleted.'));
    }
}
