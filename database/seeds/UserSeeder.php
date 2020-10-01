<?php

use App\Models\User;
use App\Models\UserProfile;
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
            'password' => 'demo1234',
        ]);

        UserProfile::forceCreate(['user_id' => 1]);
    }
}
