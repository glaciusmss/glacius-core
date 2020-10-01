<?php

namespace Database\Factories;

use App\Models\Shop;
use App\Models\User;
use App\Models\UserShop;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserShopFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserShop::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'shop_id' => Shop::factory(),
        ];
    }
}
