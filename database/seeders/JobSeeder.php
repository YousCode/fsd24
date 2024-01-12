<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Job;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jobs = [
            'post-producer',
            'producer',
            'assistant-editor',
            'post-production-assistant',
            '3d-artist',
            '2d-artist',
            'motion-designer',
            'vfx-artist',
            'sound-designer',
            'artistic-director',
            'colorist',
            'production-assistant',
            'editor',
        ];

        foreach($jobs as $jobName) {
            Job::create(['name' => $jobName]);
        }
    }
}
