<?php

namespace Database\Seeders;

use App\Models\Card;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class CardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 15; $i++) {
            $card = [
                'uuid' => Str::uuid(),
                'description' => "this is card",
                'status' => 0,
            ];

            Card::create($card);
        }
    }
}
