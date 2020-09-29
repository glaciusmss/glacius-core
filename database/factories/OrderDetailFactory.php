<?php

namespace Database\Factories;

use App\Order;
use App\OrderDetail;
use App\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderDetailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrderDetail::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'quantity' => $this->faker->numberBetween(1),
            'product_id' => Product::factory(),
            'order_id' => Order::factory(),
        ];
    }
}
