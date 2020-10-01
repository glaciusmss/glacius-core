<?php

namespace Database\Factories;

use App\Models\Marketplace;
use App\Models\MarketplaceIntegration;
use App\Models\Shop;
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
