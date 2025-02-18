<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'            => $this->faker->sentence(3),
            'description'     => $this->faker->paragraph,
            'completion_date' => $this->faker->date(),
            'completed'       => $this->faker->boolean,
        ];
    }
}
