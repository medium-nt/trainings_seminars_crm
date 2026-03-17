<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): RedirectResponse|View
    {
        // Клиентов перенаправляем на профиль
        if (auth()->user()->isClient()) {
            return redirect()->route('profile');
        }

        // Получить year и month из request или использовать текущие
        $selectedYear = (int) ($request->get('year') ?? now()->year);
        $selectedMonth = $request->get('month') ?? now()->month;

        $data = [
            'user' => auth()->user(),
            'title' => 'Главная страница',
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'years' => range(2026, now()->year),
            'months' => [
                'all_months' => '12 месяцев',
                1 => 'Январь', 2 => 'Февраль', 3 => 'Март', 4 => 'Апрель',
                5 => 'Май', 6 => 'Июнь', 7 => 'Июль', 8 => 'Август',
                9 => 'Сентябрь', 10 => 'Октябрь', 11 => 'Ноябрь', 12 => 'Декабрь',
            ],
        ];

        // Для админа добавляем статистику по группам
        if (auth()->user()->isAdmin()) {
            $stats = $this->getDashboardStats($selectedYear, $selectedMonth);

            if ($selectedMonth === 'all_months') {
                $data['statsByMonth'] = $stats['byMonth'];
            } else {
                $data['stats'] = $stats['groups'];
            }
            $data['totalContracts'] = $stats['totalContracts'];
            $data['totalPaid'] = $stats['totalPaid'];
            $data['totalDebt'] = $stats['totalDebt'];
        }

        return view('home', $data);
    }

    /**
     * Получить статистику по группам для дашборда.
     *
     * @param  int  $year  Год для фильтрации групп
     * @param  int|string  $month  Месяц для фильтрации или 'all_months' для всех месяцев
     */
    private function getDashboardStats(int $year, int|string $month): array
    {
        // Режим "12 месяцев"
        if ($month === 'all_months') {
            return $this->getStatsForAllMonths($year);
        }

        // Режим одного месяца
        return $this->getStatsForSingleMonth($year, (int) $month);
    }

    /**
     * Получить статистику за все месяцы года.
     */
    private function getStatsForAllMonths(int $year): array
    {
        $allGroups = Group::with(['clients', 'payments'])
            ->whereYear('start_date', $year)
            ->get();

        $byMonth = [];
        $totalContracts = 0;
        $totalPaid = 0;
        $totalDebt = 0;

        for ($m = 1; $m <= 12; $m++) {
            $groupsInMonth = $allGroups->filter(fn ($g) => $g->start_date->month == $m);

            $stats = $groupsInMonth->map(function (Group $group) {
                $totalCost = $group->clients->sum('pivot.price');
                $paid = $group->payments()->sum('amount');
                $debt = max(0, $totalCost - $paid);

                return [
                    'id' => $group->id,
                    'title' => $group->title,
                    'comment' => $group->note,
                    'contracts' => $totalCost,
                    'paid' => $paid,
                    'debt' => $debt,
                ];
            });

            $byMonth[$m] = [
                'groups' => $stats,
                'contracts' => $stats->sum('contracts'),
                'paid' => $stats->sum('paid'),
                'debt' => $stats->sum('debt'),
            ];

            $totalContracts += $byMonth[$m]['contracts'];
            $totalPaid += $byMonth[$m]['paid'];
            $totalDebt += $byMonth[$m]['debt'];
        }

        return [
            'byMonth' => $byMonth,
            'totalContracts' => $totalContracts,
            'totalPaid' => $totalPaid,
            'totalDebt' => $totalDebt,
        ];
    }

    /**
     * Получить статистику за один месяц.
     */
    private function getStatsForSingleMonth(int $year, int $month): array
    {
        // Группы, которые начались в выбранном году и месяце
        $groups = Group::with(['clients', 'payments'])
            ->whereYear('start_date', $year)
            ->whereMonth('start_date', $month)
            ->get();

        $stats = $groups->map(function (Group $group) {
            // Общая стоимость всех клиентов в группе (по договорам)
            $totalCost = $group->clients->sum('pivot.price');

            // Сумма всех платежей
            $paid = $group->payments()->sum('amount');

            // Долг = стоимость - оплачено
            $debt = max(0, $totalCost - $paid);

            return [
                'id' => $group->id,
                'title' => $group->title,
                'comment' => $group->note,
                'contracts' => $totalCost,
                'paid' => $paid,
                'debt' => $debt,
            ];
        });

        return [
            'groups' => $stats,
            'totalContracts' => $stats->sum('contracts'),
            'totalPaid' => $stats->sum('paid'),
            'totalDebt' => $stats->sum('debt'),
        ];
    }
}
