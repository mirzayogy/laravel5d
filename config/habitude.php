<?php

return [
    // Release stage shown as a badge next to the logo. Use "production" to hide the badge and the preview banner.
    'stage' => env('HABITUDE_STAGE', 'beta'),

    // Where beta testers can report problems.
    'feedback_url' => env('HABITUDE_FEEDBACK_URL', 'https://github.com/dzakwannajmi/habitude/issues'),

    // Interface languages. The key is the Laravel locale, the label is shown in the language's own words.
    'locales' => ['en' => 'English', 'id' => 'Bahasa Indonesia'],

    // Timezones offered in the profile and onboarding forms.
    'timezones' => [
        'Asia/Jakarta' => 'WIB (Jakarta)',
        'Asia/Makassar' => 'WITA (Makassar)',
        'Asia/Jayapura' => 'WIT (Jayapura)',
        'UTC' => 'UTC',
    ],

    // Profile pictures (DiceBear "Waves", CC0). Animation: fastest, fast, medium, slow, slowest, none.
    'avatar' => [
        'animation' => 'slow',
        'backgrounds' => ['ff5900', 'e04e00', 'ff7a33', 'c2410c', 'f97316'], // Ember family, hex without #
        'wave' => 'ffffff',
    ],

    // Units offered when creating a habit.
    'units' => ['times', 'glasses', 'pages', 'minutes', 'hours', 'steps', 'km', 'sessions'],

    // Sensible target and unit for a habit created from just a name and a category (quick add).
    'defaults' => [
        'health-fitness' => ['target' => 1, 'unit' => 'sessions', 'example' => 'Go to the gym'],
        'mindfulness' => ['target' => 10, 'unit' => 'minutes', 'example' => 'Meditate'],
        'productivity' => ['target' => 60, 'unit' => 'minutes', 'example' => 'Deep work'],
        'better-sleep' => ['target' => 1, 'unit' => 'times', 'example' => 'Sleep before 11 pm'],
        'stay-hydrated' => ['target' => 8, 'unit' => 'glasses', 'example' => 'Drink water'],
        'read-more' => ['target' => 10, 'unit' => 'pages', 'example' => 'Read a chapter'],
        'social-connections' => ['target' => 1, 'unit' => 'times', 'example' => 'Message a friend'],
        'self-care' => ['target' => 1, 'unit' => 'times', 'example' => 'Skincare routine'],
    ],

    // Units that get a timer in the daily list.
    'timed_units' => ['minutes'],

    // Mood scale labels, 1 to 5.
    'moods' => [1 => 'Awful', 2 => 'Bad', 3 => 'Okay', 4 => 'Good', 5 => 'Great'],

    // Journal prompts offered after a mood check-in, by how the day felt.
    'prompts' => [
        'low' => ['What made today hard, and what would help tomorrow?', 'What is one small thing that could make tomorrow gentler?', 'What do you need right now?'],
        'mid' => ['What is one thing you noticed today?', 'What did you do today that you want to remember?', 'How would you describe today in one sentence?'],
        'high' => ['What went well today?', 'What are you proud of today?', 'Who or what made today good?'],
    ],

    // Achievement criteria types: key => label.
    'criteria' => [
        'logs' => 'Completed check-ins',
        'streak' => 'Day streak',
        'moods' => 'Mood check-ins',
        'journals' => 'Journal entries',
        'habits' => 'Habits created',
    ],

    // Starter habits offered during onboarding, per category slug: [name, daily target, unit].
    'starters' => [
        'health-fitness' => [['Workout', 1, 'sessions'], ['Walk', 8000, 'steps']],
        'mindfulness' => [['Meditate', 10, 'minutes'], ['Gratitude note', 1, 'times']],
        'productivity' => [['Deep work', 60, 'minutes'], ['Plan the day', 1, 'times']],
        'better-sleep' => [['Sleep before 11 pm', 1, 'times'], ['No screens in bed', 1, 'times']],
        'stay-hydrated' => [['Drink water', 8, 'glasses']],
        'read-more' => [['Read', 10, 'pages']],
        'social-connections' => [['Message a friend', 1, 'times']],
        'self-care' => [['Skincare', 1, 'times'], ['Stretch', 5, 'minutes']],
    ],
];
