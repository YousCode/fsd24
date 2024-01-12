<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Luka',
            'firstname' => 'Luka',
            'lastname' => 'Doncic',
            'email' => 'doncic@mavericks.com',
            'email_verified_at' => now(),
            'password' => bcrypt('soleil123'),
            'token_login' => bcrypt('luka'),
            'user_status' => 'freelance',
            'is_admin' => 1,
        ]);

        $user->assignRole('talent');

        $user = User::create([
            'name' => 'Kyrie',
            'firstname' => 'Kyrie',
            'lastname' => 'Irving',
            'email' => 'irving@mavericks.com',
            'email_verified_at' => now(),
            'password' => bcrypt('soleil123'),
            'token_login' => bcrypt('kyrie'),
            'user_status' => 'freelance',
            'is_admin' => 1,
        ]);

        $user->assignRole('talent');

        $user = User::create([
            'name' => 'Joel',
            'firstname' => 'Joël',
            'lastname' => 'Embiid',
            'email' => 'embiid@sixers.com',
            'email_verified_at' => now(),
            'password' => bcrypt('soleil123'),
            'token_login' => bcrypt('embiid'),
            'user_status' => 'freelance',
            'is_admin' => 1,
        ]);

        $user->assignRole('talent');

        $user = User::create([
            'name' => 'Jokic',
            'firstname' => 'Nikola',
            'lastname' => 'Jokic',
            'email' => 'jokic@nuggets.com',
            'email_verified_at' => now(),
            'password' => bcrypt('soleil123'),
            'token_login' => bcrypt('jokic'),
            'user_status' => 'freelance',
            'is_admin' => 1,
        ]);

        $user = User::create([
            'name' => 'James',
            'firstname' => 'LeBron',
            'lastname' => 'James',
            'email' => 'lebron@lakers.com',
            'email_verified_at' => now(),
            'password' => bcrypt('soleil123'),
            'token_login' => bcrypt('lebron'),
            'user_status' => 'freelance',
            'is_admin' => 1,
        ]);

        $user = User::create([
            'name' => 'Curry',
            'firstname' => 'Stephen',
            'lastname' => 'Curry',
            'email' => 'curry@warriors.com',
            'email_verified_at' => now(),
            'password' => bcrypt('soleil123'),
            'token_login' => bcrypt('curry'),
            'user_status' => 'freelance',
            'is_admin' => 1,
        ]);

        $user->assignRole('talent');
    }
}
