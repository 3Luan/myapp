<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name' => 'Thành Luân',
                'email' => 'admin@gmail.com',
                'phone' => '123456789',
                'pic' => null,
                'password' => Hash::make('123123'),
                'role_id' => 1,
                'email_verified' => true,
                'is_locked' => false,
            ],
            [
                'name' => 'Luân',
                'email' => 'manager@gmail.com',
                'phone' => '987654321',
                'pic' => null,
                'password' => Hash::make('123123'),
                'role_id' => 2,
                'email_verified' => true,
                'is_locked' => false,
            ],
            [
                'name' => 'Thành Luân',
                'email' => 'member@gmail.com',
                'phone' => '555666777',
                'pic' => null,
                'password' => Hash::make('123123'),
                'role_id' => 3,
                'email_verified' => true,
                'is_locked' => false,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
