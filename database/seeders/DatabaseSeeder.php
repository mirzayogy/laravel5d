<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Task;
use App\Models\Subtask;
use App\Models\TaskDelay;
use App\Models\RescuePlan;
use App\Models\FocusSession;
use App\Models\Notification;
use App\Models\UserStatistic;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat 3 user
        User::factory(3)->create()->each(function ($user) {

            // Setiap user memiliki 3 kategori
            $categories = Category::factory(3)->create([
                'user_id' => $user->id,
            ]);

            // Setiap user memiliki 5 task
            for ($i = 0; $i < 5; $i++) {

                $task = Task::factory()->create([
                    'user_id' => $user->id,
                    'category_id' => $categories->random()->id,
                ]);

                // Setiap task memiliki 3 subtask
                Subtask::factory(3)->create([
                    'task_id' => $task->id,
                ]);

                // Riwayat penundaan
                TaskDelay::factory()->create([
                    'task_id' => $task->id,
                ]);

                // Rescue plan
                RescuePlan::factory()->create([
                    'task_id' => $task->id,
                ]);

                // Focus session
                FocusSession::factory(2)->create([
                    'user_id' => $user->id,
                    'task_id' => $task->id,
                ]);

                // Notification
                Notification::factory(2)->create([
                    'user_id' => $user->id,
                    'task_id' => $task->id,
                ]);
            }

            // Statistik user
            UserStatistic::factory(3)->create([
                'user_id' => $user->id,
            ]);
        });
    }
}
