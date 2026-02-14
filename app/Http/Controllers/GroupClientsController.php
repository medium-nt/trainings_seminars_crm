<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;

class GroupClientsController extends Controller
{
    public function create(Group $group)
    {
        return view('groups.clients.add', [
            'title' => "Добавить слушателей в группу: {$group->title}",
            'group' => $group,
        ]);
    }

    public function store(Request $request, Group $group)
    {
        $request->validate([
            'clients' => 'required|array',
            'clients.*' => 'exists:users,id',
        ], [
            'clients.required' => 'Пожалуйста, выберите хотя бы одного слушателя',
            'clients.*.exists' => 'Выбранный пользователь не существует',
        ]);

        // Добавляем только тех клиентов, которых еще нет в группе
        $existingClientIds = $group->clients()->pluck('users.id')->toArray();
        $newClientIds = array_diff($request->clients, $existingClientIds);

        if (! empty($newClientIds)) {
            $group->clients()->attach($newClientIds);
        }

        return redirect()
            ->route('groups.show', $group->id)
            ->with('success', 'Слушатели успешно добавлены в группу');
    }

    public function destroy(Group $group, User $user)
    {
        $group->clients()->detach($user->id);

        return redirect()
            ->route('groups.show', $group->id)
            ->with('success', 'Слушатель удален из группы');
    }

    public function updatePrice(Request $request, Group $group, User $user)
    {
        $request->validate([
            'price' => 'nullable|numeric|min:0|max:99999999.99',
        ], [
            'price.numeric' => 'Цена должна быть числом',
            'price.min' => 'Цена не может быть отрицательной',
            'price.max' => 'Слишком большое значение цены',
        ]);

        $group->clients()->updateExistingPivot($user->id, [
            'price' => $request->price ? number_format($request->price, 2, '.', '') : null,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'price' => $request->price,
                'formatted' => $request->price ? number_format($request->price, 2, '.', ' ').' ₽' : '---',
            ]);
        }

        return redirect()
            ->route('groups.show', $group->id)
            ->with('success', 'Цена обновлена');
    }
}
