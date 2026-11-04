<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Services\AchievementService;
use Carbon\CarbonImmutable;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Http\Request;

class JournalEntryController extends Controller
{
    public function index(Request $request)
    {
        $entries = $request->user()->journalEntries()->with('moodEntry')
            ->orderByDesc('entry_date')->orderByDesc('id')->get()
            ->groupBy(fn ($e) => $e->entry_date->format('F Y'));

        return view('journal.index', compact('entries'));
    }

    public function create(Request $request)
    {
        $today = $request->user()->today();
        $date = $this->validDate($request->query('date'), $today);
        $level = (int) $request->query('mood');

        // A gentle question that fits how the day felt, when we arrive from a mood check-in
        $prompt = null;
        if ($level >= 1 && $level <= 5) {
            $band = $level <= 2 ? 'low' : ($level === 3 ? 'mid' : 'high');
            $prompt = __(collect(config("habitude.prompts.{$band}"))->random());
        }

        return view('journal.create', ['today' => $today, 'date' => $date, 'prompt' => $prompt]);
    }

    private function validDate(?string $value, CarbonImmutable $today): CarbonImmutable
    {
        try {
            $date = $value ? CarbonImmutable::createFromFormat('!Y-m-d', $value) : $today;
        } catch (InvalidFormatException) {
            return $today;
        }

        return $date && $date->format('Y-m-d') === $value && $date <= $today ? $date : $today;
    }

    public function store(Request $request, AchievementService $achievements)
    {
        $user = $request->user();
        $data = $this->validated($request);

        // Link to that day's mood, if it exists and has no journal yet (one-to-one).
        $mood = $user->moodEntries()->whereDate('entry_date', $data['entry_date'])->whereDoesntHave('journalEntry')->first();

        $user->journalEntries()->create($data + ['mood_entry_id' => $mood?->id]);
        $achievements->award($user);

        return redirect()->route('journal.index')->with('success', __('Journal entry saved.'));
    }

    public function edit(Request $request, JournalEntry $journal)
    {
        $this->authorizeOwner($request, $journal);

        return view('journal.edit', ['journal' => $journal->load('moodEntry'), 'today' => $request->user()->today()]);
    }

    public function update(Request $request, JournalEntry $journal)
    {
        $this->authorizeOwner($request, $journal);

        $journal->update($this->validated($request));

        return redirect()->route('journal.index')->with('success', __('Journal entry updated.'));
    }

    public function destroy(Request $request, JournalEntry $journal)
    {
        $this->authorizeOwner($request, $journal);

        $journal->delete();

        return redirect()->route('journal.index')->with('success', __('Journal entry deleted.'));
    }

    private function authorizeOwner(Request $request, JournalEntry $journal): void
    {
        abort_unless($journal->user_id === $request->user()->id, 404);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'entry_date' => ['required', 'date_format:Y-m-d', 'before_or_equal:'.$request->user()->today()->toDateString()],
            'title' => ['required', 'string', 'max:120'],
            'content' => ['required', 'string', 'max:5000'],
        ], [
            'title.required' => __('Give your entry a title.'),
            'content.required' => __('Write something first.'),
        ]);
    }
}
