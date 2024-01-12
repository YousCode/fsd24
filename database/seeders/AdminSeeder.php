<?php

namespace Database\Seeders;

use App\Models\Job;
use App\Models\Workspace;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use App\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Admin',
            'lastname'=> 'kolab',
            'firstname'=>'admin',
            'email' => 'alexandre@kolab.app',
            'email_verified_at' => now(),
            'password' => bcrypt('soleil123'),
            'is_admin' => 1,
            'job_id'=>Job::pluck('id')->random(),
            'is_explorer'=>1
        ]);
        Role::create(["name" => "talent", "guard_name" => "web"]);
        Role::create(["name" => "client", "guard_name" => "web"]);
        Role::create(["name" => "admin", "guard_name" => "web"]);
        $user->assignRole('admin');

        Workspace::create([
            'name' => 'Admin workspace',
            'owner_id' => $user->id,
        ]);

        DB::table('workspace_members')->insert([
            'user_id' => 1,
            'workspace_id' => 1,
        ]);

        return $user;
    }
}
