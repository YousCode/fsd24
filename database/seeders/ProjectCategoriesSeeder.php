<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProjectCategory;

class ProjectCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            ['name' => 'music-video', 'color' => '{"bgColor": "#591E1E", "borderColor": "#DF4B4B"}'],
            ['name' => 'short-film', 'color' => '{"bgColor": "#1C3F37", "borderColor": "#459E89"}'],
            ['name' => 'advertising', 'color' => '{"bgColor": "#172265", "borderColor": "#2E44CA"}'],
            ['name' => 'documentary', 'color' => '{"bgColor": "#511466", "borderColor": "#CA33FF"}'],
            ['name' => 'report', 'color' => '{"bgColor": "#534D1B", "borderColor": "#BAAE3E"}'],
            ['name' => 'TV-program', 'color' => '{"bgColor": "#703E21", "borderColor": "#DF7B42"}'],
            ['name' => 'interview', 'color' => '{"bgColor": "#386CBB", "borderColor": "#4E8CE9"}'],
            ['name' => 'corporate-film', 'color' => '{"bgColor": "#962850", "borderColor": "#C94C79"}'],
            ['name' => 'mission-explorer', 'color' => '{"bgColor": "linear-gradient(90deg, #EB725D -7.38%, #DB57AD 109.84%)", "borderColor": " #DB57AD"}'],
            ['name' => 'film', 'color' => '{"bgColor": "#4A8835", "borderColor": "#37D300"}'],
        ];

        foreach ($categories as $category) {
            ProjectCategory::create(['name' => $category['name'], 'color' => $category['color']]);
        }
    }
}
