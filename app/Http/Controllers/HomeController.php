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
        $selectedYear = $request->get('year') ?? now()->year;
        $selectedMonth = $request->get('month') ?? now()->month;

        $data = [
            'user' => auth()->user(),
            'title' => 'Главная страница',
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'years' => range(2026, now()->year),
            'months' => [
                1 => 'Январь', 2 => 'Февраль', 3 => 'Март', 4 => 'Апрель',
                5 => 'Май', 6 => 'Июнь', 7 => 'Июль', 8 => 'Август',
                9 => 'Сентябрь', 10 => 'Октябрь', 11 => 'Ноябрь', 12 => 'Декабрь',
            ],
        ];

        // Для админа добавляем статистику по группам
        if (auth()->user()->isAdmin()) {
            $stats = $this->getDashboardStats($selectedYear, $selectedMonth);

            $data['stats'] = $stats['groups'];
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
     * @param  int  $month  Месяц для фильтрации групп
     */
    private function getDashboardStats(int $year, int $month): array
    {
        // Начало и конец выбранного месяца
        $periodStart = now()->setYear($year)->setMonth($month)->startOfMonth();
        $periodEnd = now()->setYear($year)->setMonth($month)->endOfMonth();

        // Группы, которые начались в выбранном году и месяце
        $groups = Group::with(['clients', 'payments'])
            ->whereYear('start_date', $year)
            ->whereMonth('start_date', $month)
            ->get();

        $stats = $groups->map(function (Group $group) use ($periodStart, $periodEnd) {
            // Общая стоимость всех клиентов в группе (по договорам)
            $totalCost = $group->clients->sum('pivot.price');

            // Сумма платежей за выбранный период
            $totalPaid = $group->payments()
//                ->whereBetween('payment_date', [$periodStart, $periodEnd])
                ->sum('amount');

            // Долг = стоимость - оплачено
            $debt = max(0, $totalCost - $totalPaid);

            return [
                'id' => $group->id,
                'title' => $group->title,
                'comment' => $group->note,
                'contracts' => $totalCost,
                'paid' => $totalPaid,
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
