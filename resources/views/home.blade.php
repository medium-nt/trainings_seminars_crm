@extends('adminlte::page')

@section('title', $title)
@section('content_header')
    <h1>{{ $title }}</h1>
@stop

@section('content')
    @if(auth()->user()->isAdmin() && isset($stats))
        <!-- Дашборд админа -->
        <div class="card">
            <div class="card-body">
                <!-- Заголовок -->
                <h5 class="card-title mb-3">Финансовая статистика по группам</h5>

                <!-- ИТОГО сверху -->
                <table class="table table-bordered mb-3">
                    <tbody>
                        <tr class="fw-bold">
                            <td>ИТОГО</td>
                            <td class="text-end">{{ number_format($totalPaid, 2, '.', ' ') }} ₽</td>
                            <td class="text-end {{ $totalDebt > 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($totalDebt, 2, '.', ' ') }} ₽
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Таблица групп -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Группа</th>
                                <th>Комментарий</th>
                                <th class="text-end">Оплачено</th>
                                <th class="text-end">Долг</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($stats->count() > 0)
                                @foreach($stats as $row)
                                    <tr>
                                        <td>{{ $row['title'] }}</td>
                                        <td>{{ $row['comment'] }}</td>
                                        <td class="text-end">{{ number_format($row['paid'], 2, '.', ' ') }} ₽</td>
                                        <td class="text-end {{ $row['debt'] > 0 ? 'text-danger' : 'text-success' }}">
                                            {{ number_format($row['debt'], 2, '.', ' ') }} ₽
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">
                                        Нет групп
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body">
                @if(auth()->user()->isAdmin())
                    <p>Тестовые пользователи:</p>

                    1@1.ru | 111111 - админ
                    <br>
                    <br>
                    2@2.ru | 222222 - менеджер
                    <br>
                    <br>
                    3@3.ru | 333333 - клиент
                    <br>
                    <br>
                    4@4.ru | 444444 - преподаватель
                    <br>
                @endif

                @if(auth()->user()->isBlocked())
                    <div class="alert alert-danger" role="alert">
                        <h4 class="alert-heading">Внимание!</h4>
                        <p>Ваш аккаунт заблокирован. Обратитесь к администратору.</p>
                    </div>
                @endif
            </div>
        </div>
    @endif
@endsection

@section('css')

@stop

@section('js')

@stop
