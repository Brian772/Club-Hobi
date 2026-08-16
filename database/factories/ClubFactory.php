<?php

namespace Database\Factories;

use App\Models\Club;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Club>
 */
class ClubFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'photography',
            'fishing',
            'hiking',
            'reading',
            'gaming',
            'traveling',
            'cooking',
            'fitness',
        ];
        return [
            'id' =>(string) Str::uuid(),
            'name' => fake()->company().' Club',
            'description' => fake()->paragraph(),
            'cover_url' => fake()->imageUrl(640, 480, 'hobbies', true),
            'category' => fake()->randomElement($categories),
            'created_at' => now(),
        ];
    }
}