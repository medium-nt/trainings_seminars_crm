<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserFormRequest;
use App\Models\Group;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UsersController
{
    public function profile()
    {
        $documentTypes = [
            ['type' => 'contract', 'title' => 'Договор'],
            ['type' => 'personal_data_consent', 'title' => 'Согласие на обработку ПД'],
            ['type' => 'passport_main', 'title' => 'Паспорт (основная страница)'],
            ['type' => 'passport_reg', 'title' => 'Паспорт (прописка)'],
            ['type' => 'snils', 'title' => 'СНИЛС'],
            ['type' => 'diploma_basis', 'title' => 'Документ-основание для диплома'],
            ['type' => 'name_change_document', 'title' => 'Документ о смене фамилии'],
        ];

        return view('users.profile', [
            'title' => 'Профиль',
            'user' => auth()->user(),
            'documentTypes' => $documentTypes,
            'documents' => auth()->user()->documents->keyBy('id'),
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
        $documentTypes = [
            ['type' => 'contract', 'title' => 'Договор'],
            ['type' => 'personal_data_consent', 'title' => 'Согласие на обработку ПД'],
            ['type' => 'passport_main', 'title' => 'Паспорт (основная страница)'],
            ['type' => 'passport_reg', 'title' => 'Паспорт (прописка)'],
            ['type' => 'snils', 'title' => 'СНИЛС'],
            ['type' => 'diploma_basis', 'title' => 'Документ-основание для диплома'],
            ['type' => 'name_change_document', 'title' => 'Документ о смене фамилии'],
        ];

        return view('users.edit', [
            'title' => 'Редактирование пользователя',
            'user' => $user,
            'documentTypes' => $documentTypes,
            'documents' => $user->documents->keyBy('id'),
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

    public function destroy(User $user)
    {
        if ($user->isClient() && $user->studentGroups()->count() > 0) {
            return back()
                ->with('error', 'Нельзя удалить клиента, который является слушателем в одной из групп!');
        }

        if ($user->isTeacher() && $user->groups()->count() > 0) {
            return back()
                ->with('error', 'Нельзя удалить преподавателя, который закреплен за одной из групп!');
        }

        $user->delete();

        return redirect()
            ->route($user->isClient() ? 'users.clients' : 'users.employees')
            ->with('success', 'Пользователь успешно удалён');
    }

    public function toggleBlock(User $user)
    {
        $user->update(['is_blocked' => ! $user->is_blocked]);

        return back()
            ->with('success', $user->is_blocked ? 'Пользователь заблокирован' : 'Пользователь разблокирован');
    }

    public function search(Request $request)
    {
        $search = $request->get('search', '');

        $clients = User::searchClients($search)->get();

        $results = $clients->map(function ($client) {
            return [
                'id' => $client->id,
                'full_name' => $client->full_name,
                'email' => $client->email,
            ];
        });

        return response()->json($results);
    }
}
