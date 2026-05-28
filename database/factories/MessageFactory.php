<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'conversation_id' => Conversation::factory(),
            'sender_id' => User::factory(),
            'body' => fake()->realText(rand(10, 200)),
            'file_path' => null,
            'is_read' => fake()->boolean(70),
            'deleted_by_superadmin' => false,
            'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'updated_at' => fn(array $attrs) => $attrs['created_at'],
        ];
    }

    public function unread(): static
    {
        return $this->state(fn() => ['is_read' => false]);
    }

    public function read(): static
    {
        return $this->state(fn() => ['is_read' => true]);
    }
}
