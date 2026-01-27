<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Schedule;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $group = Group::where('title', 'ГЗ-001')->first();

        if (!$group) {
            return;
        }

        $startOfWeek = Carbon::now()->startOfWeek();

        $subjects = [
            ['name' => 'Лекция', 'startHour' => 10, 'endHour' => 11, 'startMinute' => 0, 'endMinute' => 30],
            ['name' => 'Практика', 'startHour' => 12, 'endHour' => 13, 'startMinute' => 0, 'endMinute' => 30],
        ];

        // 7 дней недели
        for ($day = 0; $day < 7; $day++) {
            $currentDate = $startOfWeek->copy()->addDays($day);

            foreach ($subjects as $subject) {
                Schedule::create([
                    'group_id' => $group->id,
                    'start' => $currentDate->copy()->setTime($subject['startHour'], $subject['startMinute']),
                    'end' => $currentDate->copy()->setTime($subject['endHour'], $subject['endMinute']),
                    'status' => 'active',
                ]);
            }
        }
    }
}
