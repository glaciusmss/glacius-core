<?php

namespace Database\Factories;

use App\Enums\MarketplaceEnum;
use App\Models\Marketplace;
use Illuminate\Database\Eloquent\Factories\Factory;

class MarketplaceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Marketplace::class;

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
