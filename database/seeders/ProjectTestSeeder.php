<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\ExplorerMissionProposition;
use App\Models\ExplorerMissionConversation;
use App\Models\ExplorerDrive;
use App\Models\ExplorerDriveLink;
use App\Models\Export;
use App\Models\ProjectFile;

class ProjectTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $exports = [
            [
                'name' => 'Film principal',
                'duration' => '00:01:30',
                //'resolution_id' => '',
                'format_id' => 1,
                'language_id' => 1,
                'project_id' => 1,
                'format_raw' => '1920x1080',
            ],
            [
                'name' => 'Cutdown miroir',
                'duration' => '00:00:45',
                //'resolution_id' => '',
                'format_id' => 1,
                'language_id' => 1,
                'project_id' => 1,
                'format_raw' => '1920x1080',
            ],
            [
                'name' => 'Cutdown miroir bleu',
                'duration' => '00:00:45',
                //'resolution_id' => '',
                'format_id' => 1,
                'language_id' => 1,
                'project_id' => 1,
                'format_raw' => '1080x1350',
            ],
            [
                'name' => 'Cutdown miroir rouge',
                'duration' => '00:00:45',
                //'resolution_id' => '',
                'format_id' => 1,
                'language_id' => 1,
                'project_id' => 1,
                'format_raw' => '1080x1350',
            ],
            [
                'name' => 'Cutdown miroir orange',
                'duration' => '00:00:30',
                //'resolution_id' => '',
                'format_id' => 2,
                'language_id' => 1,
                'project_id' => 1,
                'format_raw' => '1080x1350',
            ],
            [
                'name' => 'Miroir bleu instagram',
                'duration' => '00:00:30',
                //'resolution_id' => '',
                'format_id' => 2,
                'language_id' => 1,
                'project_id' => 1,
                'format_raw' => '1080x1920',
            ],
            [
                'name' => 'Miroir rouge instagram',
                'duration' => '00:00:10',
                //'resolution_id' => '',
                'format_id' => 2,
                'language_id' => 1,
                'project_id' => 1,
                'format_raw' => '1080x1920',
            ],
            [
                'name' => 'Miroir bleu tik tok',
                'duration' => '00:00:10',
                //'resolution_id' => '',
                'format_id' => 1,
                'language_id' => 1,
                'project_id' => 1,
                'format_raw' => '1080x1920',
            ],
            [
                'name' => 'Miroir rouge tik tok',
                'duration' => '00:00:10',
                //'resolution_id' => '',
                'format_id' => 1,
                'language_id' => 1,
                'project_id' => 1,
                'format_raw' => '1080x1920',
            ],
        ];
        $now = date("Y-m-d");
        Project::create(['id' => 1, 'name' => 'Welcome to KOLAB', 'date_deadline' => $now, 'production' => 'KOLAB Production', 'category_id' => 1, 'sharing_token' => 'fri733de', 'client_phone' => '0600000000', 'client_email' => 'client@kolab.app', 'responsable_name' => 'Alexandre Pham', 'responsable_phone' => '0700000000', 'responsable_email' => 'responsable@kolab.app']);
        ExplorerMissionProposition::create(['id' => 1, 'kolab_project_id' => 1]);
        ExplorerMissionConversation::create(['id' => 1, 'id_proposition' => 1]);
        ExplorerDrive::create(['id' => 1, 'id_proposition' => 1]);
        ExplorerDriveLink::create(['id' => 1, 'id_drive' => 1, 'name' => 'Kolab', 'link' => 'https://kolab.app/sharing/project/fri733de', 'miniature' => 'https://paume.paris/images/logo/paume.jpg']);
        ProjectFile::create([
            'name' => 'Helvetica.ttc',
            'path' => '/var/www/vhosts/kolab.app/httpdocs/storage/uploads/project/Helvetica.ttc',
            'extension' => 'ttc',
            'uniqid' => '637fb1245f16c',
            'url_view' => env('APP_URL') . '/project-file/637fb1245f16c',
            'url_download' => env('APP_URL') . '/project-file/637fb1245f16c/download',
            'project_id' => 1,
        ]);
        ProjectFile::create([
            'name' => 'Livrables_PUB.pdf',
            'path' => '/var/www/vhosts/kolab.app/httpdocs/storage/uploads/project/Livrables_PUB.pdf',
            'extension' => 'pdf',
            'uniqid' => '637fb1245f16a',
            'url_view' => env('APP_URL') . '/project-file/637fb1245f16a',
            'url_download' => env('APP_URL') . '/project-file/637fb1245f16a/download',
            'project_id' => 1,
        ]);
        ProjectFile::create([
            'name' => 'LOGO_LOREM.svg',
            'path' => '/var/www/vhosts/kolab.app/httpdocs/storage/uploads/project/LOGO_LOREM.svg',
            'extension' => 'svg',
            'uniqid' => '637fb1245f16b',
            'url_view' => env('APP_URL') . '/project-file/637fb1245f16b',
            'url_download' => env('APP_URL') . '/project-file/637fb1245f16b/download',
            'project_id' => 1,
        ]);
        foreach($exports as $export) {
            Export::create($export);
        }
    }
}