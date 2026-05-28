<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConversationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => null,
            'type' => 'direct',
            'created_by' => User::factory(),
        ];
    }

    public function group(): static
    {
        return $this->state(fn() => [
            'name' => fake()->words(3, true),
            'type' => 'group',
        ]);
    }
}
