<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Seeder;

class GroupUserSeeder extends Seeder
{
    public function run(): void
    {
        $group = Group::where('title', 'ГЗ-001')->first();

        if (!$group) {
            return;
        }

        // Найти новых клиентов по email
        $client1 = User::where('email', '5@5.ru')->first();
        $client2 = User::where('email', '6@6.ru')->first();

        if ($client1) {
            $group->clients()->attach($client1->id);
        }

        if ($client2) {
            $group->clients()->attach($client2->id);
        }
    }
}
