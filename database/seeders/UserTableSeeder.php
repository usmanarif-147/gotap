<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        for ($i = 0; $i < 10; $i++) {
            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->email,
                'password' => bcrypt('11223344'),
            ]);

            Profile::create([
                'user_id' => $user->id,
                'username' => $faker->username,
                'phone' => $faker->phoneNumber,
                'active' => 1,
                'private' => 0
            ]);

            Group::create([
                'user_id' => $user->id,
                'title' => 'favourites',
            ]);

            Group::create([
                'user_id' => $user->id,
                'title' => 'scanned card',
            ]);
        }
    }
}
