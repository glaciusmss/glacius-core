<?php

namespace Database\Factories;

use App\Customer;
use App\Marketplace;
use App\Order;
use App\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'total_price' => $this->faker->randomFloat(2, 1),
            'subtotal_price' => $this->faker->randomFloat(2, 1),
            'meta' => [],
            'shop_id' => Shop::factory(),
            'marketplace_id' => Marketplace::factory(),
            'customer_id' => Customer::factory()
        ];
    }

    public function withoutCustomer()
    {
        return $this->state(function (array $attributes) {
            return [
                'customer_id' => null
            ];
        });
    }
}
