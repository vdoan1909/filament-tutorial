<?php

namespace Database\Factories;

use App\Models\State;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\City>
 */
class CityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'state_id' => State::all()->random()->id,
            'team_id' => Team::all()->random()->id,
            'name' => fake()->name
        ];
    }
}
