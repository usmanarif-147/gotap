<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $users = User::pluck('id')->toArray();
        for ($i = 0; $i < 20; $i++) {
            Profile::create([
                'user_id' => $users[array_rand($users)],
                'name' => $faker->name,
                'email' => $faker->email,
                'username' => $faker->username,
                'phone' => $faker->phoneNumber,
                'active' => 0,
                'private' => 0
            ]);
        }
    }
}
