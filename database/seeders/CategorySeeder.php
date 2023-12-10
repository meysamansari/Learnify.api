<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create(['name' => 'طراحی']);
        Category::create(['name' => 'امنیت و شبکه']);
        Category::create(['name' => 'برنامه نویسی']);
        Category::create(['name' => 'فیلم و انیمیشن']);
        Category::create(['name' => 'نرم افزار های کاربردی']);
        Category::create(['name' => 'نرم افزار های مهندسی']);
        Category::create(['name' => 'توسعه موبایل']);
        Category::create(['name' => 'توسعه وب']);
    }
}
