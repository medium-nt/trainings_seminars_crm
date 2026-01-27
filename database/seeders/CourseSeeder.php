<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        Course::create(['title' => 'Госзакупки']);
        Course::create(['title' => 'Бухгалтерия']);
        Course::create(['title' => 'Маркетплейсы']);
    }
}
