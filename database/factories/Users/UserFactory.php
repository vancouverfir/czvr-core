<?php

namespace Database\Factories\Users;

use App\Models\Users\User;
use App\Support\Authorization\SystemRoleCatalog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'id' => fake()->unique()->numberBetween(100000, 9999999),
            'fname' => $firstName = fake()->firstName(),
            'lname' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'remember_token' => Str::random(10),
            'init' => 1,
            'display_fname' => $firstName,
            'gdpr_subscribed_emails' => 1,
        ];
    }

    public function controller(): static
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole(SystemRoleCatalog::CONTROLLER));
    }

    public function mentor(): static
    {
        return $this->controller()
            ->afterCreating(fn (User $user) => $user->assignRole(SystemRoleCatalog::MENTOR));
    }

    public function instructor(): static
    {
        return $this->controller()
            ->afterCreating(fn (User $user) => $user->assignRole(SystemRoleCatalog::INSTRUCTOR));
    }

    public function staff(): static
    {
        return $this->controller()
            ->afterCreating(fn (User $user) => $user->assignRole(SystemRoleCatalog::STAFF));
    }

    public function administrator(): static
    {
        return $this->controller()
            ->afterCreating(fn (User $user) => $user->assignRole(SystemRoleCatalog::ADMINISTRATOR));
    }
}
