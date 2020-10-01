<?php

namespace Database\Factories;

use App\Enums\TokenType;
use App\Token;
use Illuminate\Database\Eloquent\Factories\Factory;

class TokenFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Token::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'token' => $this->faker->asciify(str_repeat('*', 16)),
            'type' => $this->faker->randomElement(TokenType::getValues()),
            'meta' => [],
            'expired_at' => now()->addMinutes(15),
        ];
    }
}
