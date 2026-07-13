<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Super Admin',
                'email'    => 'superadmin@gmail.com',
                'password' => Hash::make('admin123'),
                'is_active' => 1,
            ],
            [
                'name'     => 'Staf Akademik',
                'email'    => 'akademik@gmail.com',
                'password' => Hash::make('admin123'),
                'is_active' => 1,
            ],
            [
                'name'     => 'Pengajar',
                'email'    => 'pengajar@gmail.com',
                'password' => Hash::make('admin123'),
                'is_active' => 1,
            ],
            [
                'name'     => 'Pelajar',
                'email'    => 'pelajar@gmail.com',
                'password' => Hash::make('admin123'),
                'is_active' => 1,
            ],
            [
                'name'     => 'Pimpinan',
                'email'    => 'pimpinan@gmail.com',
                'password' => Hash::make('admin123'),
                'is_active' => 1,
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->insert(array_merge($user, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Assign role ke masing-masing user
        // role_id: 1=super_admin, 2=akademik, 3=pengajar, 4=pelajar, 5=pimpinan
        $assignments = [
            ['model_id' => 1, 'role_id' => 1], // superadmin
            ['model_id' => 2, 'role_id' => 2], // akademik
            ['model_id' => 3, 'role_id' => 3], // pengajar
            ['model_id' => 4, 'role_id' => 4], // pelajar
            ['model_id' => 5, 'role_id' => 5], // pimpinan
        ];

        foreach ($assignments as $assign) {
            DB::table('role_user')->insert([
                'role_id'    => $assign['role_id'],
                'model_id'   => $assign['model_id'],
                'model_type' => 'App\\Models\\User',
            ]);
        }
    }
}
