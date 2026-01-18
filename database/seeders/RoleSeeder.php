<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['name' => 'client']);
        Role::create(['name' => 'manager']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'teacher']);
    }
}
