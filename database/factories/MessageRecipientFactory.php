<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageRecipientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'message_id' => Message::factory(),
            'recipient_id' => User::factory(),
            'seen' => $this->faker->boolean(),
            'seen_at' => $this->faker->boolean() ? Carbon::now() : null,
        ];
    }
}
