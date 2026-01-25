<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CoursesController extends Controller
{
    public function index()
    {
        return view('courses.index', [
            'title' => 'Курсы',
            'courses' => Course::query()->paginate(5),
        ]);
    }

    public function create()
    {
        return view('courses.create', [
            'title' => 'Создание курса',
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|string|min:2|max:255|unique:courses,title',
        ];

        $text = [
            'title.required' => 'Пожалуйста, введите название курса',
            'title.min' => 'Название должно быть не менее 2 символов',
            'title.max' => 'Название должно быть не более 255 символов',
            'title.unique' => 'Курс с таким названием уже существует',
        ];

        $validatedData = $request->validate($rules, $text);

        if (Course::create($validatedData)) {
            return redirect()
                ->route('courses.index')
                ->with('success', 'Курс успешно создан');
        }

        return back()->with('error', 'Ошибка создания курса');
    }

    public function edit(Course $course)
    {
        return view('courses.edit', [
            'title' => 'Редактирование курса',
            'course' => $course,
        ]);
    }

    public function update(Request $request, Course $course)
    {
        $rules = [
            'title' => 'required|string|min:2|max:255|unique:courses,title,' . $course->id,
        ];

        $text = [
            'title.required' => 'Пожалуйста, введите название курса',
            'title.min' => 'Название должно быть не менее 2 символов',
            'title.max' => 'Название должно быть не более 255 символов',
            'title.unique' => 'Курс с таким названием уже существует',
        ];

        $validatedData = $request->validate($rules, $text);

        if ($course->update($validatedData)) {
            return redirect()
                ->route('courses.index')
                ->with('success', 'Курс успешно обновлен');
        }

        return back()->with('error', 'Ошибка обновления курса');
    }

    public function destroy(Course $course)
    {
        if ($course->delete()) {
            return redirect()
                ->route('courses.index')
                ->with('success', 'Курс успешно удален');
        }

        return back()->with('error', 'Ошибка удаления курса');
    }
}
