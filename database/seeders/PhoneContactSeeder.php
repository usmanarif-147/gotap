<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\DB;

class PhoneContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $users = User::pluck('id')->toArray();
        for ($i = 0; $i < 50; $i++) {
            DB::table('phone_contacts')->insert([
                'user_id' => $users[array_rand($users)],
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => $faker->email,
                'phone' => $faker->phoneNumber,
                'photo' => null,
                'work_email' => $faker->email,
                'work_phone' => $faker->phoneNumber,
                'website' => $faker->url,
                'company_name' => $faker->company,
                'address' => $faker->address
            ]);
        }
    }
}
