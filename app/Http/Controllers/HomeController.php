<?php

namespace App\Http\Controllers;

use App\Models\Group;

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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Клиентов перенаправляем на профиль
        if (auth()->user()->isClient()) {
            return redirect()->route('profile');
        }

        $data = [
            'user' => auth()->user(),
            'title' => 'Главная страница',
        ];

        // Для админа добавляем статистику по группам
        if (auth()->user()->isAdmin()) {
            $stats = $this->getDashboardStats();
            $data['stats'] = $stats['groups'];
            $data['totalPaid'] = $stats['totalPaid'];
            $data['totalDebt'] = $stats['totalDebt'];
        }

        return view('home', $data);
    }

    /**
     * Получить статистику по группам для дашборда.
     */
    private function getDashboardStats(): array
    {
        $currentYearStart = now()->startOfYear();
        $currentYearEnd = now()->endOfDay();

        // Только группы текущего года
        $groups = Group::with(['clients', 'payments'])
            ->whereYear('start_date', now()->year)
            ->get();

        $stats = $groups->map(function (Group $group) use ($currentYearStart, $currentYearEnd) {
            // Общая стоимость всех клиентов в группе
            $totalCost = $group->clients->sum('pivot.price');

            // Сумма платежей за текущий год
            $totalPaid = $group->payments()
                ->whereBetween('payment_date', [$currentYearStart, $currentYearEnd])
                ->sum('amount');

            // Долг = стоимость - оплачено
            $debt = max(0, $totalCost - $totalPaid);

            return [
                'id' => $group->id,
                'title' => $group->title,
                'paid' => $totalPaid,
                'debt' => $debt,
            ];
        });

        return [
            'groups' => $stats,
            'totalPaid' => $stats->sum('paid'),
            'totalDebt' => $stats->sum('debt'),
        ];
    }
}
