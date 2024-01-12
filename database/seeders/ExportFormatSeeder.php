<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Format;

class ExportFormatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Format::create(['name' => '422'. PHP_EOL .'HQ']);
        Format::create(['name' => 'H264']);
        Format::create(['name' => 'ProRes']);
        Format::create(['name' => "Sqc". PHP_EOL .".images"]);
    }
}
