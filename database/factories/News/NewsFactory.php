<?php

namespace Database\Factories\News;

use App\Models\News\News;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsFactory extends Factory
{
    protected $model = News::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'user_id' => User::factory(),
            'show_author' => fake()->boolean(),
            'image' => null,
            'content' => fake()->paragraphs(3, true),
            'summary' => fake()->sentence(),
            'published' => fake()->dateTimeBetween('-1 year'),
            'edited' => null,
            'visible' => true,
            'email_level' => 0,
            'certification' => 0,
            'slug' => fake()->unique()->slug(),
        ];
    }
}
