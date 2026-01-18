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
        User::factory()->create([
            'name' => 'Админ',
            'email' => '1@1.ru',
            'password' => bcrypt('111111'),
            'role_id' => 3,
            'phone' => '11111111',
        ]);

        User::factory()->create([
            'name' => 'Менеджер',
            'email' => '2@2.ru',
            'password' => bcrypt('222222'),
            'role_id' => 2,
            'phone' => '22222222',
        ]);

        User::factory()->create([
            'name' => 'Клиент',
            'email' => '3@3.ru',
            'password' => bcrypt('333333'),
            'role_id' => 1,
            'phone' => '33333333',
        ]);

        User::factory()->create([
            'name' => 'Преподаватель',
            'email' => '4@4.ru',
            'password' => bcrypt('444444'),
            'role_id' => 4,
            'phone' => '44444444',
        ]);
    }
}
