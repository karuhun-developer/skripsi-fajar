<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
            'phone' => '+628123456789',
            'city' => 'Jakarta',
            'ward' => 'Ciperna',
        ]);

        $user->assignRole('admin');
    }
}
