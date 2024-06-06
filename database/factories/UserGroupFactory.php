<?php

namespace Database\Factories;

use App\Models\UserGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserGroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = UserGroup::class;
    public function definition()
    {
        return [
            'name' => $this->faker->word,
            
        ];
    }
}
