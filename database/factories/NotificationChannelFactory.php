<?php

namespace Database\Factories;

use App\Enums\MarketplaceEnum;
use App\Models\NotificationChannel;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationChannelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NotificationChannel::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->randomElement(MarketplaceEnum::getValues()),
            'website' => $this->faker->url,
        ];
    }
}
