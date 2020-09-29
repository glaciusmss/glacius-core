<?php

namespace Database\Factories;

use App\Marketplace;
use App\MarketplaceIntegration;
use App\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

class MarketplaceIntegrationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MarketplaceIntegration::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'token' => $this->faker->asciify(str_repeat('*', 16)),
            'refreshToken' => $this->faker->asciify(str_repeat('*', 16)),
            'meta' => [],
            'shop_id' => Shop::factory(),
            'marketplace_id' => Marketplace::factory(),
        ];
    }
}
