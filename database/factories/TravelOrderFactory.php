<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TravelOrder>
 */
class TravelOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'requestor_name' => $this->faker->name,
            'destination' => $this->faker->city,
            'departure_date' => $this->faker->dateTimeBetween('+1 week', '+2 weeks')->format('Y-m-d'),
            'return_date' => $this->faker->dateTimeBetween('+2 weeks', '+1 month')->format('Y-m-d'),
            'status' => 'pending',
            'requestor_id' => User::factory(),
        ];
    }
}
