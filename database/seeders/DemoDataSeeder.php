<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Challenge;
use App\Models\Competition;
use App\Models\Hint;
use App\Models\Profile;
use App\Models\Tag;
use App\Models\Team;
use App\Models\User;
use App\Models\Writeup;
use App\Notifications\AppNotification;
use Carbon\Carbon;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Fills the application with a lot of demo data: teams, players, competitions in every
 * phase, challenges with hints and tags, a believable history of submissions, writeups,
 * and notifications. Demo logins: admin@example.com, player@example.com (captain),
 * member@example.com, solo@example.com (no team), unverified@example.com. The password is
 * "password". The flag of every challenge is FLAG{demo_<challenge id>}.
 */
class DemoDataSeeder extends Seeder
{
    private Generator $faker;

    /** @var array<string, list<array{string, string}>> */
    private array $bank = [
        'Web' => [
            ['Cookie Monster', 'The admin panel trusts whatever the cookie says. Can you convince it that you are the admin?'],
            ['Robots Only', 'Search engines are told to stay away from some pages. You are not a search engine.'],
            ['Union Station', "This login form builds its SQL by gluing strings together.\n\nFind the way in without knowing a password."],
            ['Reflected Feelings', 'The search box echoes whatever you type. Make it echo something it should not.'],
            ['Header Hunt', 'The server leaks more in its response headers than in its pages.'],
            ['Token Trouble', 'Sessions are signed, but are they signed well? Forge a token for the user `admin`.'],
            ['Upload Party', 'The avatar upload accepts images only. At least, that is what it says.'],
            ['Template Tantrum', 'The greeting page renders your name through a template engine. It renders a little too much.'],
            ['Cache Me If You Can', "A caching proxy sits in front of the app.\n\nMake it serve one user's page to another."],
        ],
        'Cryptography' => [
            ['Caesar\'s Salad', 'A friend sent you a message and a note that says "rotate it". Ancient problems, ancient solutions.'],
            ['Base Camp', 'Three layers of encoding, one flag at the end. Peel them one by one.'],
            ['XOR of Two Worlds', 'A single byte key was reused for the whole message. That is not a lot of keys to try.'],
            ['Small Exponent, Big Mistake', 'RSA with `e = 3` and a short message. The modulus never even gets a chance to wrap.'],
            ['Vigenere\'s Revenge', 'A repeating key hides the message. The length of the key hides in the repeats.'],
            ['ECB Penguin', 'Same block, same ciphertext. The encrypted image still looks like a penguin.'],
            ['Hash Browns', 'The password hash is plain unsalted MD5. Rainbow tables love this.'],
            ['Padding Oracle', 'The server tells you whether the padding is valid. That is all you need to decrypt.'],
            ['Common Factors', 'Two RSA moduli share a prime. You are one `gcd` away from the private key.'],
        ],
        'Forensics' => [
            ['Strings Attached', 'A suspicious file hides plain text in plain sight. Look closely.'],
            ['Metadata Matters', 'The photo was taken somewhere. Where, exactly?'],
            ['Packet Party', 'A capture file with an hour of traffic. One conversation is not like the others.'],
            ['Hidden in Pixels', 'The image looks ordinary, but the least significant bits tell another story.'],
            ['Deleted but Not Gone', 'A disk image holds a file somebody wanted to delete.'],
            ['Memory Lane', 'A memory dump from a busy workstation. Find the process that should not be running.'],
            ['Zip Zip Hooray', 'The archive is password protected, and the password is on the sticky note you found.'],
            ['Log Jam', 'Thousands of web server log lines and a single successful attack.'],
            ['Timeline', 'Reconstruct what the attacker did, minute by minute, from the file system timestamps.'],
        ],
        'Reverse Engineering' => [
            ['Crackme One', 'A tiny binary asks for a password. It compares it in the most obvious way.'],
            ['Hello, Disassembler', 'Open the binary, follow the `main` function, read the flag.'],
            ['Key Generator', 'The license check can be reversed into a key generator.'],
            ['Obfuscated Strings', 'Every string is XORed at runtime. Break on the right function and watch.'],
            ['Python Bytecode', 'A compiled Python file protects the flag. Decompile it.'],
            ['Anti-Debug', 'The program exits the moment a debugger shows up. Politely ask it to stay.'],
            ['Stack Machine', 'A custom virtual machine runs the check. Learn its instructions.'],
            ['Packed', 'The binary unpacks itself in memory. Catch it right after the unpacking.'],
            ['Z3 Says Yes', 'A pile of constraints describes the flag. Let a solver do the arithmetic.'],
        ],
        'Pwn' => [
            ['Buffer Buddy', 'A classic stack buffer overflow with no protections. Overwrite the return address.'],
            ['Format Follies', 'The program calls `printf` with your input as the format string. Leak the flag from the stack.'],
            ['Ret2Win', 'There is a `win` function. You just have to jump to it.'],
            ['Shellcode Shop', 'The program runs your bytes. Choose them wisely.'],
            ['Canary in the Coal Mine', 'A stack canary guards the return address. Leak it, then smash.'],
            ['ROP Chef', 'No executable stack, no problem. Cook a ROP chain.'],
            ['Heap of Trouble', 'A use-after-free in a tiny note-taking service.'],
            ['GOT Milk', 'Overwrite a GOT entry and redirect a call.'],
            ['Sigreturn Surprise', 'A fake signal frame is all you need to control every register.'],
        ],
        'Miscellaneous' => [
            ['Welcome Aboard', 'Every great journey starts with a flag. Yours is hiding on the rules page.'],
            ['QR Quest', 'A damaged QR code still contains a flag. Repair it.'],
            ['Morse Mischief', 'An audio file beeps in a pattern you may recognise.'],
            ['Jail Break', 'A Python sandbox blocks the obvious builtins. Not the clever ones.'],
            ['OSINT Odyssey', 'A photo of a cafe, a sign, and a tweet. Find the street.'],
            ['Esoteric Evening', 'The program is written in a language with only eight instructions.'],
            ['Regex Riddle', 'Craft the shortest regular expression that matches exactly the right strings.'],
            ['Trivia Night', 'Ten questions about security history. Search engines are allowed, we know you will.'],
            ['Scripting 101', 'Solve 500 maths problems in under ten seconds. Humans need not apply.'],
        ],
    ];

    /** @var array<string, list<string>> */
    private array $tagPool = [
        'Web' => ['beginner', 'sql-injection', 'xss', 'jwt'],
        'Cryptography' => ['beginner', 'rsa', 'aes', 'xor'],
        'Forensics' => ['beginner', 'pcap', 'steganography', 'memory'],
        'Reverse Engineering' => ['beginner', 'python', 'ghidra', 'z3'],
        'Pwn' => ['beginner', 'rop', 'heap', 'shellcode'],
        'Miscellaneous' => ['beginner', 'osint', 'scripting', 'sandbox'],
    ];

    /** @var array<string, list<string>> */
    private array $hintBank = [
        'Web' => ['Look at what the browser sends and receives.', 'The developer tools are your best friend here.', 'Try the simplest payload first, then get creative.', 'Somebody forgot to validate this on the server.'],
        'Cryptography' => ['The algorithm is a classic. Read about it first.', 'Check the length of everything: key, message, modulus.', 'A short script beats doing it by hand.', 'The mistake is in how the primitive is used, not in the primitive.'],
        'Forensics' => ['Run the basic tools before the clever ones.', 'Sort by time, then by size.', 'Not everything that is hidden is encrypted.', 'The interesting part is small, so zoom in.'],
        'Reverse Engineering' => ['Start at the entry point and follow the strings.', 'A decompiler saves a lot of reading.', 'Watch the comparison, not the input.', 'Patch the check if the check is the obstacle.'],
        'Pwn' => ['Find out how many bytes it takes to crash the program.', 'Check which protections are on.', 'A debugger with a nice stack view helps a lot.', 'You only need to control one value.'],
        'Miscellaneous' => ['Read the description once more, slowly.', 'The answer is closer than you think.', 'There is a library for that.', 'Think about what the author wanted you to try.'],
    ];

    /** @var list<string> */
    private array $teamNames = [
        'Null Pointers', 'Byte Me', 'Root Beer', 'Segfault Squad', 'Packet Pirates', 'Binary Bandits', 'Stack Smashers',
        'Cipher Punks', 'Hash Browns', 'Kernel Panic', 'Ctrl+Alt+Elite', 'Sudo Make Me', 'Ghost in the Shell', 'Cookie Monsters',
    ];

    /** @var list<int> */
    private array $teamSizes = [4, 4, 3, 3, 3, 3, 3, 2, 3, 2, 2, 3, 2, 2];

    /** @var list<string> */
    private array $institutions = [
        'UNISKA MAB', 'Universitas Lambung Mangkurat', 'Universitas Gadjah Mada', 'Institut Teknologi Bandung', 'Telkom University',
        'Universitas Indonesia', 'Politeknik Negeri Banjarmasin', 'Binus University', 'Universitas Brawijaya',
    ];

    /** @var list<string> */
    private array $bios = [
        'Loves reverse engineering and strong coffee.',
        'Web first, everything else second.',
        'Learning pwn one segfault at a time.',
        'Cryptography nerd. Ask me about elliptic curves.',
        'Forensics, OSINT, and a little bit of scripting.',
        'Here for the flags and the snacks.',
        'Rubber duck debugger by day, CTF player by night.',
    ];

    public function run(): void
    {
        mt_srand(2026);
        $this->faker = FakerFactory::create('id_ID');
        $this->faker->seed(2026);

        $tags = $this->seedTags();
        $teams = $this->seedPeople();
        $competitions = $this->seedCompetitions();
        $challenges = $this->seedChallenges($competitions, $tags);
        $this->seedRegistrations($teams, $competitions);
        $this->seedActivity($teams, $competitions, $challenges);
        $this->seedWriteups();
        $this->seedNotifications($teams);
    }

    /**
     * @return array<string, int>
     */
    private function seedTags(): array
    {
        $names = collect($this->tagPool)->flatten()->unique()->sort()->values();

        return $names->mapWithKeys(fn (string $name) => [$name => Tag::create(['name' => $name])->id])->all();
    }

    /**
     * @return list<Team>
     */
    private function seedPeople(): array
    {
        User::factory()->create(['name' => 'Admin', 'email' => 'admin@example.com', 'role' => 'admin']);
        $this->profile(User::where('email', 'admin@example.com')->first(), 'Keeps the competitions running smoothly.');

        $solo = User::factory()->create(['name' => 'Solo Player', 'email' => 'solo@example.com', 'role' => 'player']);
        $this->profile($solo, 'Looking for a team to join.');

        $unverified = User::factory()->unverified()->create(['name' => 'Unverified User', 'email' => 'unverified@example.com', 'role' => 'player']);
        $this->profile($unverified, 'Still waiting for the verification email.');

        $teams = [];

        foreach ($this->teamNames as $index => $name) {
            $team = Team::create(['name' => $name, 'invite_code' => Str::upper(Str::random(8))]);
            $members = [];

            for ($i = 0; $i < $this->teamSizes[$index]; $i++) {
                $attributes = ['role' => 'player', 'team_id' => $team->id, 'name' => $this->faker->name()];

                if ($index === 0 && $i === 0) {
                    $attributes = ['name' => 'Player One', 'email' => 'player@example.com'] + $attributes;
                } elseif ($index === 0 && $i === 1) {
                    $attributes = ['name' => 'Member Two', 'email' => 'member@example.com'] + $attributes;
                }

                $user = User::factory()->create($attributes);
                $this->profile($user, $this->bios[array_rand($this->bios)]);
                $members[] = $user;
            }

            $team->update(['captain_id' => $members[0]->id]);
            $teams[] = $team;
        }

        return $teams;
    }

    private function profile(User $user, string $bio): void
    {
        Profile::create([
            'user_id' => $user->id,
            'bio' => $bio,
            'institution' => $this->institutions[array_rand($this->institutions)],
            'github_url' => 'https://github.com/'.Str::slug($user->name, ''),
        ]);
    }

    /**
     * @return array<string, Competition>
     */
    private function seedCompetitions(): array
    {
        $rows = [
            'spring' => ['Flagboard Spring Open', 'running', now()->subHours(30), now()->addHours(42),
                "The main event of the season. Twenty-four challenges across six categories, a live scoreboard, and bragging rights.\n\nHints cost points, so think before you click."],
            'cup' => ['Campus Cyber Cup 2026', 'running', now()->subDays(12), now()->subDays(10),
                'The inter-campus finals. Twelve challenges, two days, a lot of coffee. The competition has ended, and the scoreboard and writeups are open for everyone.'],
            'night' => ['Night Owls Mini CTF', 'running', now()->addDays(3)->setTime(19, 0), now()->addDays(4)->setTime(7, 0),
                'A twelve hour sprint for night owls. Register your team now, the challenges unlock the moment the clock starts.'],
            'warmup' => ['Weekly Warm-up #12', 'finished', now()->subDays(7), now()->subDays(6),
                'A short warm-up with one challenge per category, ended early by the admins. Perfect for new players.'],
            'winter' => ['Winter Warmup (draft)', 'draft', now()->addDays(30), now()->addDays(31),
                'Still being prepared. Only admins can see this competition.'],
        ];

        $competitions = [];

        foreach ($rows as $key => [$title, $status, $start, $end, $description]) {
            $competitions[$key] = Competition::create([
                'title' => $title,
                'slug' => Str::slug($title),
                'description' => $description,
                'start_at' => $start,
                'end_at' => $end,
                'status' => $status,
            ]);
        }

        return $competitions;
    }

    /**
     * @param  array<string, Competition>  $competitions
     * @param  array<string, int>  $tags
     * @return array<string, list<Challenge>>
     */
    private function seedChallenges(array $competitions, array $tags): array
    {
        $difficulty = ['easy', 'easy', 'medium', 'hard', 'easy', 'medium', 'medium', 'hard', 'hard'];
        $points = [100, 100, 200, 300, 100, 250, 200, 350, 400];

        // Which entries of every category go to which competition.
        $plan = [
            'spring' => ['entries' => [0, 1, 2, 3], 'published' => true],
            'cup' => ['entries' => [4, 5], 'published' => true],
            'warmup' => ['entries' => [6], 'published' => true],
            'night' => ['entries' => [7], 'published' => true],
        ];

        $made = [];
        $categories = Category::orderBy('id')->get()->keyBy('name');

        $create = function (string $key, Category $category, int $entry, bool $published) use (&$made, $competitions, $difficulty, $points, $tags) {
            [$title, $description] = $this->bank[$category->name][$entry];

            $challenge = Challenge::create([
                'competition_id' => $competitions[$key]->id,
                'category_id' => $category->id,
                'title' => $title,
                'description' => $description,
                'points' => $points[$entry],
                'flag_hash' => 'pending',
                'difficulty' => $difficulty[$entry],
                'is_published' => $published,
            ]);

            $challenge->update(['flag_hash' => Hash::make('FLAG{demo_'.$challenge->id.'}', ['rounds' => 4])]);

            $pool = $this->tagPool[$category->name];
            $picked = array_unique([$pool[0] === 'beginner' && $difficulty[$entry] === 'easy' ? 'beginner' : $pool[1 + $entry % 3], $pool[1 + ($entry + 1) % 3]]);
            $challenge->tags()->attach(array_map(fn (string $name) => $tags[$name], $picked));

            $bank = $this->hintBank[$category->name];
            Hint::create(['challenge_id' => $challenge->id, 'content' => $bank[$entry % 4], 'cost' => 10]);
            Hint::create(['challenge_id' => $challenge->id, 'content' => $bank[($entry + 1) % 4], 'cost' => 25 + 5 * ($entry % 3)]);

            $made[$key][] = $challenge;
        };

        foreach ($plan as $key => $spec) {
            foreach ($categories as $category) {
                foreach ($spec['entries'] as $entry) {
                    $create($key, $category, $entry, $spec['published']);
                }
            }
        }

        // Two extra challenges for the night competition and three unpublished drafts for the winter one.
        $create('night', $categories['Web'], 8, true);
        $create('night', $categories['Cryptography'], 8, true);

        foreach (['Forensics', 'Reverse Engineering', 'Pwn'] as $name) {
            $create('winter', $categories[$name], 8, false);
        }

        return $made;
    }

    /**
     * @param  list<Team>  $teams
     * @param  array<string, Competition>  $competitions
     */
    private function seedRegistrations(array $teams, array $competitions): void
    {
        $counts = ['spring' => 12, 'cup' => 10, 'night' => 6, 'warmup' => 6];
        $rows = [];

        foreach ($counts as $key => $count) {
            $competition = $competitions[$key];

            foreach (array_slice($teams, 0, $count) as $position => $team) {
                $rows[] = [
                    'competition_id' => $competition->id,
                    'team_id' => $team->id,
                    'registered_at' => Carbon::parse($competition->start_at)->subDays(2)->addMinutes($position * 37)->min(now()->subHours(($position + 1) * 5)),
                ];
            }
        }

        DB::table('competition_team')->insert($rows);
    }

    /**
     * A believable history: stronger teams solve more, hard challenges fall less often,
     * and hints are bought now and then. Every team solves a challenge at most once.
     *
     * @param  list<Team>  $teams
     * @param  array<string, Competition>  $competitions
     * @param  array<string, list<Challenge>>  $challenges
     */
    private function seedActivity(array $teams, array $competitions, array $challenges): void
    {
        $registered = ['spring' => 12, 'cup' => 10, 'warmup' => 6];
        $skill = [0.78, 0.9, 0.55, 0.7, 0.6, 0.45, 0.35, 0.82, 0.5, 0.3, 0.65, 0.4, 0.25, 0.2];
        $factor = ['easy' => 0.95, 'medium' => 0.62, 'hard' => 0.32];
        $submissions = [];
        $unlocks = [];

        foreach ($registered as $key => $count) {
            $competition = $competitions[$key];
            $start = Carbon::parse($competition->start_at);
            $end = min(Carbon::parse($competition->end_at), now());
            $window = max(60, $start->diffInSeconds($end));

            foreach (array_slice($teams, 0, $count) as $position => $team) {
                $members = User::where('team_id', $team->id)->pluck('id')->all();

                foreach ($challenges[$key] as $challenge) {
                    $solved = mt_rand(0, 1000) / 1000 < $skill[$position] * $factor[$challenge->difficulty] * ($key === 'spring' ? 0.85 : 1);
                    $tried = $solved || mt_rand(0, 100) < 40;

                    if (! $tried) {
                        continue;
                    }

                    $at = $start->copy()->addSeconds((int) ($window * (mt_rand(4, 100) / 100) ** 1.4));
                    $wrong = $solved ? mt_rand(0, 3) : mt_rand(1, 5);

                    for ($i = 1; $i <= $wrong; $i++) {
                        $submissions[] = $this->submission($team, $challenge, $members, false, $at->copy()->subMinutes($i * mt_rand(2, 25)));
                    }

                    if ($solved) {
                        $submissions[] = $this->submission($team, $challenge, $members, true, $at);
                    }

                    if (mt_rand(0, 100) < 30) {
                        foreach ($challenge->hints()->orderBy('cost')->limit(mt_rand(1, 2))->get() as $hint) {
                            $unlocks[] = [
                                'team_id' => $team->id,
                                'hint_id' => $hint->id,
                                'unlocked_at' => $at->copy()->subMinutes(mt_rand(3, 40)),
                            ];
                        }
                    }
                }
            }
        }

        foreach (array_chunk($submissions, 200) as $chunk) {
            DB::table('submissions')->insert($chunk);
        }

        DB::table('hint_unlocks')->insert($unlocks);
    }

    /**
     * @param  list<int>  $members
     * @return array<string, mixed>
     */
    private function submission(Team $team, Challenge $challenge, array $members, bool $correct, Carbon $at): array
    {
        return [
            'team_id' => $team->id,
            'challenge_id' => $challenge->id,
            'user_id' => $members[array_rand($members)],
            'is_correct' => $correct,
            'submitted_at' => $at,
        ];
    }

    private function seedWriteups(): void
    {
        $solves = DB::table('submissions')
            ->join('challenges', 'challenges.id', '=', 'submissions.challenge_id')
            ->join('competitions', 'competitions.id', '=', 'challenges.competition_id')
            ->where('submissions.is_correct', true)
            ->orderBy('submissions.submitted_at')
            ->select('submissions.team_id', 'submissions.user_id', 'submissions.challenge_id', 'submissions.submitted_at', 'challenges.title')
            ->get();

        $templates = [
            "## Overview\n\nWe started by reading the description of **%s** carefully and writing down what we knew.\n\n## Steps\n\n1. Reproduce the behaviour locally.\n2. Narrow down the interesting input.\n3. Automate the boring part.\n\n```python\npayload = b\"A\" * 64\nprint(payload)\n```\n\n## Flag\n\nWe had the flag after about twenty minutes. Thanks to the author for a fun challenge!",
            "## TL;DR\n\nThe trick in **%s** is to stop trusting the obvious path.\n\n> The simplest idea is usually the one that works.\n\n## What we tried\n\n- Read the source (and the strings)\n- Tried the naive approach first\n- Wrote a small script when the naive approach almost worked\n\n## Takeaways\n\nKeep notes while you work. We solved this one faster the second time.",
            "## Approach\n\nA quick table of what we tried for **%s**:\n\n| Attempt | Result |\n|---|---|\n| Obvious input | Nope |\n| Edge case | Closer |\n| Script | Flag |\n\n## Final script\n\n```bash\nfor i in \$(seq 1 10); do echo \"try \$i\"; done\n```\n\nSee you in the next competition!",
        ];

        foreach ($solves->shuffle()->take(36)->values() as $index => $solve) {
            $at = Carbon::parse($solve->submitted_at)->addHours(mt_rand(1, 20))->min(now()->subMinutes(mt_rand(5, 600)));

            Writeup::unguarded(fn () => Writeup::create([
                'user_id' => $solve->user_id,
                'challenge_id' => $solve->challenge_id,
                'title' => ['How we solved', 'Notes on', 'Writeup:', 'A walkthrough of'][$index % 4].' '.$solve->title,
                'content' => sprintf($templates[$index % 3], $solve->title),
                'status' => $index % 7 === 6 ? 'draft' : 'published',
                'created_at' => $at,
                'updated_at' => $at,
            ]));
        }
    }

    /**
     * @param  list<Team>  $teams
     */
    private function seedNotifications(array $teams): void
    {
        $captain = User::where('email', 'player@example.com')->first();
        $member = User::where('email', 'member@example.com')->first();
        $solved = DB::table('submissions')
            ->join('challenges', 'challenges.id', '=', 'submissions.challenge_id')
            ->where('submissions.team_id', $teams[0]->id)
            ->where('submissions.is_correct', true)
            ->orderByDesc('submissions.submitted_at')
            ->select('challenges.id', 'challenges.title', 'challenges.points')
            ->limit(3)
            ->get();

        $captain->notify(new AppNotification('Welcome to Flagboard', 'Create or join a team, then register for a competition.', route('dashboard', absolute: false), 'flag', 'sky'));
        $captain->notifications()->latest()->first()->markAsRead();

        foreach ($solved as $row) {
            $captain->notify(new AppNotification('Your team scored', "A teammate solved {$row->title} (+{$row->points} points).", route('challenges.show', $row->id, false), 'check-circle', 'green'));
        }

        $member->notify(new AppNotification('You joined a team', 'You are now part of '.$teams[0]->name.'.', route('team.show', absolute: false), 'users', 'sky'));
        $member->notify(new AppNotification('Competition starting soon', 'Night Owls Mini CTF starts in three days.', route('competitions.index', absolute: false), 'clock', 'coral'));

        DB::table('notifications')->orderBy('created_at')->get()->each(function ($row, int $index) {
            DB::table('notifications')->where('id', $row->id)->update(['created_at' => now()->subHours(40 - $index * 6)]);
        });
    }
}
