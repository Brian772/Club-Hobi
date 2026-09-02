<?php

namespace Database\Factories;

use App\Models\Club;
use App\Models\Hobby;
use App\Models\User;
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
        return [
            'id' =>(string) Str::uuid(),
            'name' => fake()->company().' Club',
            'hobby_id' => Hobby::inRandomOrder()->first()->id,
            'description' => fake()->paragraph(),
            'created_by' => User::inRandomOrder()->first()->id,
            'cover_url' => fake()->imageUrl(640, 480, 'hobbies', true),
            'created_at' => now(),
        ];
    }
}