<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => fake()->userName(),
            'discriminator' => (string) fake()->numberBetween(1000, 9999),
            'email' => fake()->unique()->safeEmail(),
            'avatar' => fake()->optional()->sha256(),
            'verified' => fake()->boolean(),
            'locale' => fake()->randomElement(['en', 'en-US', 'de', 'fr']),
            'mfa_enabled' => fake()->boolean(),
        ];
    }
}
