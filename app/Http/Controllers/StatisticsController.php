<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    private const DOCUMENT_TYPES = [
        'contract' => 'Договор',
        'personal_data_consent' => 'Согласие ПД',
        'passport_main' => 'Паспорт (осн)',
        'passport_reg' => 'Паспорт (проп)',
        'snils' => 'СНИЛС',
        'diploma_basis' => 'Диплом основание',
        'name_change_document' => 'Смена фамилии',
    ];

    public function documents(Request $request)
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isManager()) {
            abort(403);
        }

        $groupId = $request->get('group_id');
        $showOnlyPending = $request->get('show_only_pending') === '1';

        $query = User::where('role_id', 1)
            ->with('documents');

        if ($groupId) {
            $query->whereHas('studentGroups', function ($q) use ($groupId) {
                $q->where('group_id', $groupId);
            });
        }

        $clients = $query->get();

        $statistics = $clients->map(function ($client) {
            $row = [
                'id' => $client->id,
                'full_name' => $client->full_name,
                'documents' => [],
                'hasPending' => false,
            ];

            foreach (self::DOCUMENT_TYPES as $type => $title) {
                $docs = $client->documents->where('type', $type);
                $docsCount = $docs->count();
                $approvedCount = $docs->where('is_approved', true)->count();

                $allApproved = $docsCount > 0 && $docsCount === $approvedCount;

                $row['documents'][$type] = [
                    'exists' => $docsCount > 0,
                    'all_approved' => $allApproved,
                ];

                if ($docsCount > 0 && ! $allApproved) {
                    $row['hasPending'] = true;
                }
            }

            return $row;
        });

        // Фильтрация: только те, у кого есть непроверенные
        if ($showOnlyPending) {
            $statistics = $statistics->filter(fn ($row) => $row['hasPending']);
        }

        return view('statistics.documents', [
            'title' => 'Статистика документов',
            'documentTypes' => self::DOCUMENT_TYPES,
            'statistics' => $statistics,
            'groups' => \App\Models\Group::all(),
            'selectedGroup' => $groupId,
            'showOnlyPending' => $showOnlyPending,
        ]);
    }

    // Место для будущих методов статистики
    // public function courses() { ... }
    // public function groups() { ... }
}
