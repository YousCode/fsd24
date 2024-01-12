<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([JobSeeder::class,AdminSeeder::class, UserTableSeeder::class, ProjectCategoriesSeeder::class, SkillsSeeder::class, ExportFormatSeeder::class, LangSeeder::class, ProjectTestSeeder::class]);
    }
}
