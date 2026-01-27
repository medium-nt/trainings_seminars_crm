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
            'last_name' => 'Админ',
            'name' => 'Иван',
            'patronymic' => 'Иванович',
            'email' => '1@1.ru',
            'password' => bcrypt('111111'),
            'role_id' => 3,
            'phone' => '11111111',
        ]);

        User::factory()->create([
            'last_name' => 'Менеджер',
            'name' => 'Петр',
            'patronymic' => 'Петрович',
            'email' => '2@2.ru',
            'password' => bcrypt('222222'),
            'role_id' => 2,
            'phone' => '22222222',
        ]);

        User::factory()->create([
            'last_name' => 'Клиент',
            'name' => 'Сидор',
            'patronymic' => 'Сидорович',
            'email' => '3@3.ru',
            'password' => bcrypt('333333'),
            'role_id' => 1,
            'phone' => '33333333',
        ]);

        User::factory()->create([
            'last_name' => 'Преподаватель',
            'name' => 'Семен',
            'patronymic' => 'Семенович',
            'email' => '4@4.ru',
            'password' => bcrypt('444444'),
            'role_id' => 4,
            'phone' => '44444444',
        ]);

        User::factory()->create([
            'last_name' => 'Клиент',
            'name' => 'Александр',
            'patronymic' => 'Александрович',
            'email' => '5@5.ru',
            'password' => bcrypt('555555'),
            'role_id' => 1,
            'phone' => '55555555',
        ]);

        User::factory()->create([
            'last_name' => 'Клиент',
            'name' => 'Михаил',
            'patronymic' => 'Михайлович',
            'email' => '6@6.ru',
            'password' => bcrypt('666666'),
            'role_id' => 1,
            'phone' => '66666666',
        ]);
    }
}
