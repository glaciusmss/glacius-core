<?php

namespace Database\Factories;

use App\Enums\GenderEnum;
use App\User;
use App\UserProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'phone_number' => $this->faker->phoneNumber,
            'gender' => $this->faker->randomElement(GenderEnum::getValues()),
            'date_of_birth' => $this->faker->dateTime,
            'user_id' => User::factory(),
        ];
    }
}
