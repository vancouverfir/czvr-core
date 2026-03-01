<?php

namespace Database\Factories\News;

use App\Models\News\News;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsFactory extends Factory
{
    protected $model = News::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'content' => fake()->paragraph(),
            'date' => fake()->date('Y-m-d'),
            'type' => 'Email',
            'user_id' => 1,
            'archived' => 0,
        ];
    }
}
