<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin 01 Invas',
                'email' => 'admin@admin.com',
                'password' => Hash::make('admin123'),
                'status_user' => 'admin',
                'is_admin' => 1,
            ],
            [
                'name' => 'Daffa Ramadhan',
                'email' => 'daffa@gmail.com',
                'password' => Hash::make('12345678'),
                'status_user' => 'RPL',
                'is_admin' => 0,
            ],
            [
                'name' => 'Faza Muhammad Tegar',
                'email' => 'faza@gmail.com',
                'password' => Hash::make('12345678'),
                'status_user' => 'TBSM',
                'is_admin' => 0,
            ],
            [
                'name' => 'Dhea Febrianti',
                'email' => 'dhea@gmail.com',
                'password' => Hash::make('12345678'),
                'status_user' => 'TKRO',
                'is_admin' => 0,
            ],
            [
                'name' => 'Rio Oktora',
                'email' => 'rio@gmail.com',
                'password' => Hash::make('12345678'),
                'status_user' => 'Umum',
                'is_admin' => 0,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
