<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Schedule::with('group');

        if ($request->has('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        $schedules = $query->get()->map(function ($schedule) {
            return [
                'id' => $schedule->id,
                'title' => $schedule->group->title,
                'start' => $schedule->start->format('Y-m-d\TH:i:s'),
                'end' => $schedule->end->format('Y-m-d\TH:i:s'),
                'extendedProps' => [
                    'group_id' => $schedule->group_id,
                    'group_title' => $schedule->group->title,
                    'status' => $schedule->status,
                ],
            ];
        });

        return response()->json($schedules);
    }
}
