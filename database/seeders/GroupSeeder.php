<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Course;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        $teacherId = 4; // Семен Семенович
        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();

        // Группа ГЗ-001 → курс "Госзакупки"
        Group::create([
            'title' => 'ГЗ-001',
            'course_id' => Course::where('title', 'Госзакупки')->first()->id,
            'teacher_id' => $teacherId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'in_waiting',
        ]);

        // Группа БУХ-001 → курс "Бухгалтерия"
        Group::create([
            'title' => 'БУХ-001',
            'course_id' => Course::where('title', 'Бухгалтерия')->first()->id,
            'teacher_id' => $teacherId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'in_waiting',
        ]);

        // Группа МП-001 → курс "Маркетплейсы"
        Group::create([
            'title' => 'МП-001',
            'course_id' => Course::where('title', 'Маркетплейсы')->first()->id,
            'teacher_id' => $teacherId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'in_waiting',
        ]);
    }
}
