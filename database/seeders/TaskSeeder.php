<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Task;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!app()->environment('local')) {
            $this->command->info('Task seeder is allowed only in local environment.');
            return;
        }

        if (Task::count() > 0) {
            $this->command->info('Tasks already exist. Seeder aborted.');
            return;
        }

        Task::factory()->count(50)->create();

        $this->command->info('50 tasks seeded successfully.');
    }
}
