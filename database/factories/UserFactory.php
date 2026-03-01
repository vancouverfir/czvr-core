<?php

namespace Database\Factories\Users;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'fname' => fake()->firstName(),
            'lname' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'remember_token' => Str::random(10),
            'permissions' => 0,
            'init' => 1,
            'display_fname' => fake()->firstName(),
        ];
    }
}
