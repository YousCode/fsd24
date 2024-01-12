<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Skill;
use App\Models\TaskType;

class SkillsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $skills = [
            'assistantship',
            'editing',
            'artistic-direction',
            'sound-design',
            'motion',
            'writing',
            'VFX',
            'search',
            'post-grading-confo',
            'pre-grading-confo',
            'color-grading',
            'graphism',
            '3D',
            'management',
            'project-preparation',
            'musical-composition',
            'sound-mixing',
            'archiving',
            'unarchive',
            'brief',
            'export',
            'flame'
        ];

        foreach($skills as $skill) {
            Skill::create(['name' => $skill]);
            TaskType::create(['name' => $skill]);
        }
    }
}
