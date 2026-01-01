<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Instructor;


use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Courses>
 */
class CoursesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'title' => $this->faker->sentence(3),


            'description' => $this->faker->paragraph(2),


            'image_url' => $this->faker->imageUrl(640, 480, 'courses', true),
            'level' => $this->faker->randomElement(['beginner', 'intermediate', 'advanced']),
            'videos' => $this->faker->numberBetween(10, 100),
            'requirements' => $this->faker->paragraph(4),
            'rating' => $this->faker->numberBetween(0, 5),
            'duration' => $this->faker->numberBetween(1, 20) . ' hours',


            'category_id' => Category::inRandomOrder()->first()->id ?? Category::factory(),
        ];
    }
}
