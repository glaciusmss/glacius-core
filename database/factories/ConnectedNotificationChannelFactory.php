<?php

namespace Database\Factories;

use App\ConnectedNotificationChannel;
use App\NotificationChannel;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConnectedNotificationChannelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ConnectedNotificationChannel::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'meta' => [],
            'user_id' => User::factory(),
            'notification_channel_id' => NotificationChannel::factory(),
        ];
    }
}
