<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Schedule;
use Illuminate\Http\Request;

class SchedulesController extends Controller
{
    public function index()
    {
        return view('schedules.index', [
            'title' => 'Расписание',
            'groups' => Group::all(),
        ]);
    }

    public function create(Request $request)
    {
        return view('schedules.create', [
            'title' => 'Создание занятия',
            'groups' => Group::all(),
            'start' => $request->query('start'),
            'end' => $request->query('end'),
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'group_id' => 'required|exists:groups,id',
            'start' => 'required|date',
            'end' => 'required|date|after:start',
        ];

        $text = [
            'group_id.required' => 'Пожалуйста, выберите группу',
            'group_id.exists' => 'Выбранная группа не существует',
            'start.required' => 'Пожалуйста, выберите дату и время начала',
            'end.required' => 'Пожалуйста, выберите дату и время окончания',
            'end.after' => 'Дата окончания должна быть позже даты начала',
        ];

        $validatedData = $request->validate($rules, $text);
        $validatedData['status'] = 'active';

        if (Schedule::create($validatedData)) {
            return redirect()
                ->route('schedules.index')
                ->with('success', 'Занятие успешно создано');
        }

        return back()->with('error', 'Ошибка создания занятия');
    }

    public function edit(Schedule $schedule)
    {
        return view('schedules.edit', [
            'title' => 'Редактирование занятия',
            'schedule' => $schedule->load('group'),
            'groups' => Group::all(),
        ]);
    }

    public function update(Request $request, Schedule $schedule)
    {
        $rules = [
            'group_id' => 'required|exists:groups,id',
            'start' => 'required|date',
            'end' => 'required|date|after:start',
        ];

        $text = [
            'group_id.required' => 'Пожалуйста, выберите группу',
            'group_id.exists' => 'Выбранная группа не существует',
            'start.required' => 'Пожалуйста, выберите дату и время начала',
            'end.required' => 'Пожалуйста, выберите дату и время окончания',
            'end.after' => 'Дата окончания должна быть позже даты начала',
        ];

        $validatedData = $request->validate($rules, $text);

        if ($schedule->update($validatedData)) {
            return redirect()
                ->route('schedules.index')
                ->with('success', 'Занятие успешно обновлено');
        }

        return back()->with('error', 'Ошибка обновления занятия');
    }

    public function destroy(Schedule $schedule)
    {
        if ($schedule->delete()) {
            return redirect()
                ->route('schedules.index')
                ->with('success', 'Занятие успешно удалено');
        }

        return back()->with('error', 'Ошибка удаления занятия');
    }
}
