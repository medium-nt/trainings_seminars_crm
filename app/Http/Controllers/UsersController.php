<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserFormRequest;
use App\Models\Group;
use App\Models\User;
use App\Services\UserService;

class UsersController
{
    public function profile()
    {
        return view('users.profile', [
            'title' => 'Профиль',
            'user' => auth()->user(),
        ]);
    }

    public function profileUpdate(UserFormRequest $request, UserService $userService)
    {
        $data = $userService->setPasswordIfNeeded(
            $request->validated(),
            $request->input('password')
        );

        auth()->user()->update($data);

        return redirect()
            ->route('profile')
            ->with('success', 'Изменения сохранены.');
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

    public function store(UserFormRequest $request, UserService $userService)
    {
        $data = $userService->setPasswordIfNeeded(
            $request->validated(),
            $request->input('password')
        );

        $user = User::create($data);

        return redirect()
            ->route($user->isClient() ? 'users.clients' : 'users.employees')
            ->with('success', 'Пользователь успешно создан');
    }

    public function edit(User $user)
    {
        return view('users.edit', [
            'title' => 'Редактирование пользователя',
            'user' => $user,
        ]);
    }

    public function update(UserFormRequest $request, User $user, UserService $userService)
    {
        $data = $userService->setPasswordIfNeeded(
            $request->validated(),
            $request->input('password')
        );

        $user->update($data);

        return redirect()
            ->route($user->isClient() ? 'users.clients' : 'users.employees')
            ->with('success', 'Пользователь успешно обновлён');
    }
}
