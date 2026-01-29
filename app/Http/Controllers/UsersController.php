<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController
{
    public function profile()
    {
        return view('users.profile', [
            'title' => 'Профиль',
            'user' => auth()->user(),
        ]);
    }

    public function profileUpdate(Request $request)
    {
        $rules = [
            'last_name' => 'required|string|min:2|max:255',
            'name' => 'required|string|min:2|max:255',
            'patronymic' => 'nullable|string|min:2|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'sometimes|nullable|string|min:8|max:15',
            'password' => 'nullable|confirmed|string|min:6',
        ];

        $text = [
            'last_name.required' => 'Пожалуйста, введите фамилию',
            'last_name.min' => 'Фамилия должна быть не менее 2 символов',
            'last_name.max' => 'Фамилия должна быть не более 255 символов',
            'name.required' => 'Пожалуйста, введите имя',
            'name.min' => 'Имя должно быть не менее 2 символов',
            'name.max' => 'Имя должно быть не более 255 символов',
            'patronymic.min' => 'Отчество должно быть не менее 2 символов',
            'patronymic.max' => 'Отчество должно быть не более 255 символов',
            'email.required' => 'Пожалуйста, введите адрес электронной почты',
            'email.email' => 'Пожалуйста, введите корректный адрес электронной почты',
            'email.max' => 'Адрес электронной почты должен быть не более 255 символов',
            'phone.min' => 'Номер телефона должен быть не менее 8 символов',
            'phone.max' => 'Номер телефона должен быть не более 15 символов',
            'password.min' => 'Пароль должен быть не менее 6 символов',
            'password.confirmed' => 'Пароли не совпадают',
        ];

        $validatedData = $request->validate($rules, $text);

        if ($request->filled('password')) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        $user = auth()->user();
        if ($user->update($validatedData)) {
            return redirect()->route('profile')->with('success', 'Изменения сохранены.');
        }

        return back()->with('error', 'Ошибка сохранения');
    }

    public function clients()
    {
        $search = request('search') ?? '';
        $groupId = request('group_id');
        $users = User::searchClients($search, $groupId)->paginate(5);

        return view('users.clients', [
            'title' => 'Клиенты',
            'users' => $users,
            'groups' => Group::all(),
        ]);
    }

    public function employees()
    {
        return view('users.employees', [
            'title' => 'Сотрудники',
            'users' => User::where('role_id', '!=', 1)
                ->paginate(5),
            'groups' => Group::all(),
        ]);
    }

    public function create()
    {
        return view('users.create', [
            'title' => 'Создание пользователя',
        ]);
    }

    public function store()
    {
        $rules = [
            'last_name' => 'required|string|min:2|max:255',
            'name' => 'required|string|min:2|max:255',
            'patronymic' => 'nullable|string|min:2|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|string|min:6',
            'role_id' => 'required|in:1,2,4',
        ];

        $text = [
            'last_name.required' => 'Пожалуйста, введите фамилию',
            'last_name.min' => 'Фамилия должна быть не менее 2 символов',
            'last_name.max' => 'Фамилия должна быть не более 255 символов',
            'name.required' => 'Пожалуйста, введите имя',
            'name.min' => 'Имя должно быть не менее 2 символов',
            'name.max' => 'Имя должно быть не более 255 символов',
            'patronymic.min' => 'Отчество должно быть не менее 2 символов',
            'patronymic.max' => 'Отчество должно быть не более 255 символов',
            'email.required' => 'Пожалуйста, введите адрес электронной почты',
            'email.email' => 'Пожалуйста, введите корректный адрес электронной почты',
            'email.max' => 'Адрес электронной почты должен быть не более 255 символов',
            'email.unique' => 'Пользователь с таким адресом электронной почты уже существует',
            'password.required' => 'Пожалуйста, введите пароль',
            'password.min' => 'Пароль должен быть не менее 6 символов',
            'password.confirmed' => 'Пароли не совпадают',
            'role.required' => 'Пожалуйста, выберите роль',
            'role.in' => 'Выбранная роль не существует',
        ];

        $validatedData = request()->validate($rules, $text);

        $validatedData['password'] = bcrypt($validatedData['password']);

        $user = User::create($validatedData);

        if ($user) {
            if ($user->isClient()) {
                $route = 'users.clients';
            } else {
                $route = 'users.employees';
            }

            return redirect()
                ->route($route)
                ->with('success', 'Пользователь успешно создан');
        }

        return back()->with('error', 'Ошибка создания пользователя');
    }
}
