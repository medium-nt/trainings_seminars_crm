@extends('adminlte::page')

@section('title', $title)
@section('content_header')
    <h1>{{ $title }}</h1>
@stop

@section('content')
    <!-- Фильтры -->
    @if(auth()->user()->isAdmin() && (isset($stats) || isset($statsByMonth)))
        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="year">Год</label>
                        <select name="year" id="year" class="form-control"
                                onchange="updatePageWithQueryParam(this)">
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="month">Месяц</label>
                        <select name="month" id="month" class="form-control"
                                onchange="updatePageWithQueryParam(this)">
                            @foreach($months as $num => $name)
                                <option value="{{ $num }}" {{ $selectedMonth == $num ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(auth()->user()->isAdmin() && (isset($stats) || isset($statsByMonth)))
        <!-- Дашборд админа -->
        <div class="card">
            <div class="card-body">
                <!-- Заголовок -->
                <h5 class="card-title mb-3">Финансовая статистика по группам</h5>

                <!-- ИТОГО сверху -->
                <table class="table table-bordered mb-3" style="table-layout: fixed;">
                    <tbody>
                        <tr class="fw-bold">
                            <td style="width: 55%;">ИТОГО</td>
                            <td style="width: 15%;" class="text-end">По договорам: <b>{{ number_format($totalContracts, 2, '.', ' ') }} ₽</b></td>
                            <td style="width: 15%;" class="text-end">Оплачено: <b>{{ number_format($totalPaid, 2, '.', ' ') }} ₽</b></td>
                            <td style="width: 15%;" class="text-end">Долг: <b>{{ number_format($totalDebt, 2, '.', ' ') }} ₽</b></td>
                        </tr>
                    </tbody>
                </table>

                @if(isset($statsByMonth))
                    <!-- Режим: 12 месяцев -->
                    @foreach($statsByMonth as $monthNum => $monthData)
                        @if($monthData['groups']->count() > 0)
                            <div class="mb-4">
                                <h6 class="mb-2">{{ $months[$monthNum] }} {{ $selectedYear }}</h6>

                                <table class="table table-bordered table-striped mb-0" style="table-layout: fixed;">
                                    <thead>
                                        <tr>
                                            <th style="width: 25%;">Группа</th>
                                            <th style="width: 30%;">Комментарий</th>
                                            <th style="width: 15%;" class="text-end">По договорам</th>
                                            <th style="width: 15%;" class="text-end">Оплачено</th>
                                            <th style="width: 15%;" class="text-end">Долг</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($monthData['groups'] as $row)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('groups.show', $row['id']) }}"
                                                       class="text-decoration-none">
                                                        {{ $row['title'] }}
                                                    </a>
                                                </td>
                                                <td>{{ $row['comment'] }}</td>
                                                <td class="text-end">{{ number_format($row['contracts'], 2, '.', ' ') }} ₽</td>
                                                <td class="text-end">{{ number_format($row['paid'], 2, '.', ' ') }} ₽</td>
                                                <td class="text-end {{ $row['debt'] > 0 ? 'text-danger' : 'text-success' }}">
                                                    {{ number_format($row['debt'], 2, '.', ' ') }} ₽
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    @endforeach
                @else
                    <!-- Режим: один месяц -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0" style="table-layout: fixed;">
                            <thead>
                                <tr>
                                    <th style="width: 25%;">Группа</th>
                                    <th style="width: 30%;">Комментарий</th>
                                    <th style="width: 15%;" class="text-end">По договорам</th>
                                    <th style="width: 15%;" class="text-end">Оплачено</th>
                                    <th style="width: 15%;" class="text-end">Долг</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($stats->count() > 0)
                                    @foreach($stats as $row)
                                        <tr>
                                            <td>
                                                <a href="{{ route('groups.show', $row['id']) }}"
                                                   class="text-decoration-none">
                                                    {{ $row['title'] }}
                                                </a>
                                            </td>
                                            <td>{{ $row['comment'] }}</td>
                                            <td class="text-end">{{ number_format($row['contracts'], 2, '.', ' ') }} ₽</td>
                                            <td class="text-end">{{ number_format($row['paid'], 2, '.', ' ') }} ₽</td>
                                            <td class="text-end {{ $row['debt'] > 0 ? 'text-danger' : 'text-success' }}">
                                                {{ number_format($row['debt'], 2, '.', ' ') }} ₽
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">
                                            Нет групп
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                @endif
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
    <script src="{{ asset('js/PageQueryParam.js') }}"></script>
@stop
