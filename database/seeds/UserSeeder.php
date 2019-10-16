<?php

use App\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::forceCreate([
            'name' => 'NeoSon',
            'email' => 'lkloon123@hotmail.com',
            'email_verified_at' => now(),
            'password' => 'demo1234'
        ]);
    }
}
