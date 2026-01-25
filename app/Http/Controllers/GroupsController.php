<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupRequest;
use App\Models\Course;
use App\Models\Group;
use App\Models\User;

class GroupsController extends Controller
{
    public function index()
    {
        return view('groups.index', [
            'title' => 'Группы',
            'groups' => Group::query()
                ->with('course', 'teacher')
                ->paginate(5),
        ]);
    }

    public function create()
    {
        return view('groups.create', [
            'title' => 'Создание группы',
            'courses' => Course::all(),
            'teachers' => User::teachers(),
        ]);
    }

    public function store(GroupRequest $request)
    {
        if (Group::create($request->validated())) {
            return redirect()
                ->route('groups.index')
                ->with('success', 'Группа успешно создана');
        }

        return back()->with('error', 'Ошибка создания группы');
    }

    public function edit(Group $group)
    {
        return view('groups.edit', [
            'title' => 'Редактирование группы',
            'group' => $group->load('course', 'teacher'),
            'courses' => Course::all(),
            'teachers' => User::teachers(),
        ]);
    }

    public function update(GroupRequest $request, Group $group)
    {
        if ($group->update($request->validated())) {
            return redirect()
                ->route('groups.index')
                ->with('success', 'Группа успешно обновлена');
        }

        return back()->with('error', 'Ошибка обновления группы');
    }

    public function destroy(Group $group)
    {
        if ($group->delete()) {
            return redirect()
                ->route('groups.index')
                ->with('success', 'Группа успешно удалена');
        }

        return back()->with('error', 'Ошибка удаления группы');
    }
}
