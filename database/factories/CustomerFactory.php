<?php

namespace Database\Factories;

use App\Customer;
use App\Marketplace;
use App\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'meta' => [],
            'shop_id' => Shop::factory(),
            'marketplace_id' => Marketplace::factory(),
        ];
    }
}
