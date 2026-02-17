<?php

namespace App\Http\Controllers;

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

        return view('home', [
            'user' => auth()->user(),
            'title' => 'Главная страница',
        ]);
    }
}
