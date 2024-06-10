<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'sender_id' => User::factory(),
            'content' => $this->faker->paragraph,
            'type' => $this->faker->randomElement(['individual', 'group', 'all']),
            'group_id' => null,
        ];
    }
}
