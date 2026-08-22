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
                'name' => 'Admin Inventory',
                'email' => 'admin@invas.test',
                'password' => Hash::make('password'),
                'is_admin' => 1,
            ],
            [
                'name' => 'Administrator Invas',
                'email' => 'admin@admin.com',
                'password' => Hash::make('admin123'),
                'is_admin' => 1,
            ],
            [
                'name' => 'Teknisi Andi',
                'email' => 'andi@invas.test',
                'password' => Hash::make('password'),
                'is_admin' => 0,
            ],
            [
                'name' => 'Teknisi Budi',
                'email' => 'budi@invas.test',
                'password' => Hash::make('password'),
                'is_admin' => 0,
            ],
            [
                'name' => 'Teknisi Candra',
                'email' => 'candra@invas.test',
                'password' => Hash::make('password'),
                'is_admin' => 0,
            ],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
