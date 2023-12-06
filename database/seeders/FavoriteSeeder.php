<?php

namespace Database\Seeders;

use App\Models\Favorite;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Favorite::create(['name' => 'توسعه وب']);
        Favorite::create(['name' => 'امنیت و شبکه']);
        Favorite::create(['name' => 'طراحی']);
        Favorite::create(['name' => 'فیلم و انیمیشن']);
        Favorite::create(['name' => 'نرم افزار های کاربردی']);
        Favorite::create(['name' => 'نرم افزار های مهندسی']);
        Favorite::create(['name' => 'برنامه نویسی']);
    }
}
