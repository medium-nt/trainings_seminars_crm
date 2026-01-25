<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|min:2|max:255',
            'course_id' => 'nullable|exists:courses,id',
            'teacher_id' => 'nullable|exists:users,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'note' => 'nullable|string|max:1000',
            'status' => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Пожалуйста, введите название группы',
            'title.min' => 'Название должно быть не менее 2 символов',
            'title.max' => 'Название должно быть не более 255 символов',
            'course_id.exists' => 'Выбранный курс не существует',
            'teacher_id.exists' => 'Выбранный преподаватель не существует',
            'start_date.date' => 'Некорректная дата начала',
            'end_date.date' => 'Некорректная дата окончания',
            'end_date.after' => 'Дата окончания должна быть позже даты начала',
            'note.max' => 'Заметка должна быть не более 1000 символов',
            'status.max' => 'Статус должен быть не более 50 символов',
        ];
    }
}
