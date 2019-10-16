<?php

use App\NotificationChannel;
use Illuminate\Database\Seeder;

class NotificationChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        NotificationChannel::create([
            'name' => 'telegram',
            'website' => 'https://telegram.org/',
        ]);
    }
}
