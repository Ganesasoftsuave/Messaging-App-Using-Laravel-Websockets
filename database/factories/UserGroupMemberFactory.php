<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserGroupMemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */

     
    public function definition()
    {
        return [
            'user_id' => function () {
                return User::factory()->create()->id;
            },  
            'group_id' => function () {
                return UserGroup::factory()->create()->id;
            },
            'is_subscribe' => $this->faker->boolean(),
        ];
    }
}
