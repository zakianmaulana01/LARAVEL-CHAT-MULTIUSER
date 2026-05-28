<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => 'user',
            'avatar' => null,
            'last_seen' => fake()->dateTimeBetween('-2 hours', 'now'),
            'is_banned' => false,
            'remember_token' => Str::random(10),
        ];
    }

    public function superadmin(): static
    {
        return $this->state(fn() => ['role' => 'superadmin']);
    }

    public function banned(): static
    {
        return $this->state(fn() => ['is_banned' => true]);
    }
}
