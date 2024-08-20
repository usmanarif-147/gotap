<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Platform;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = Category::pluck('id')->toArray();
        for ($i = 0; $i < 10; $i++) {

            Platform::create([
                'title'             =>  'platform_' . $i,
                'icon'              =>   null,
                'pro'               => 0,
                'category_id'       =>  $categories[array_rand($categories)],
                'status'            =>  1,
                'placeholder_en'    => 'placeholder in english',
                'placeholder_sv'    => 'placeholder in spanish',
                'description_en'    => 'description in english',
                'description_sv'    => 'placeholder in spanish',
                'baseURL'           => 'http://localhost/' . 'platform_' . $i,
                'input'             =>  'url',
            ]);
        }
    }
}
