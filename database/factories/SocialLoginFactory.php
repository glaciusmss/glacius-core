<?php

namespace Database\Factories;

use App\Enums\SocialProvider;
use App\SocialLogin;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SocialLoginFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SocialLogin::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'provider_user_id' => $this->faker->asciify(str_repeat('*', 20)),
            'provider' => $this->faker->randomElement(SocialProvider::getValues()),
            'user_id' => User::factory()
        ];
    }
}
