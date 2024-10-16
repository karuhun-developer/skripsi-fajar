<?php

namespace Database\Seeders;

use App\Models\Router;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RouterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Router::create([
            'name' => 'Mikrotik RB350',
            'ip_address' => '192.168.10.1',
            'username' => 'mikrotik',
            'password' => 'password',
            'description' => fake()->sentence(),
            'active' => true,
        ]);

        Router::create([
            'name' => 'Mikrotik RB950',
            'ip_address' => '192.168.90.1',
            'username' => 'admin',
            'password' => 'password',
            'description' => fake()->sentence(),
            'active' => true,
        ]);
    }
}
