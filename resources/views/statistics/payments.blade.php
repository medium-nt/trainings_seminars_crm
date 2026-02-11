@extends('adminlte::page')

@section('title', $title)
@section('content_header')
    <h1>{{ $title }}</h1>
@stop

@section('content')
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="group_id">Группа</label>
                    <select name="group_id" id="group_id" class="form-control"
                            onchange="updatePageWithQueryParam(this)">
                        <option value="">Все группы</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ $selectedGroup == $group->id ? 'selected' : '' }}>
                                {{ $group->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="payment_status">Статус оплаты</label>
                    <select name="payment_status" id="payment_status" class="form-control"
                            onchange="updatePageWithQueryParam(this)">
                        <option value="all" {{ $paymentStatus === 'all' ? 'selected' : '' }}>Все клиенты</option>
                        <option value="paid" {{ $paymentStatus === 'paid' ? 'selected' : '' }}>Все оплачено</option>
                        <option value="unpaid" {{ $paymentStatus === 'unpaid' ? 'selected' : '' }}>Есть неоплата</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Клиент</th>
                            <th>Группа</th>
                            <th>Стоимость</th>
                            <th>Оплачено</th>
                            <th>Остаток</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($statistics->count() > 0)
                            @foreach($statistics as $row)
                                <tr>
                                    <td class="py-2">
                                        <a href="{{ route('users.edit', $row['client_id']) }}"
                                           target="_blank"
                                           class="text-decoration-none">
                                            {{ $row['client_name'] }}
                                        </a>
                                    </td>
                                    <td>{{ $row['group_title'] }}</td>
                                    <td>{{ number_format($row['price'], 2, '.', ' ') }} ₽</td>
                                    <td>{{ number_format($row['paid'], 2, '.', ' ') }} ₽</td>
                                    <td class="{{ $row['remaining'] > 0 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format($row['remaining'], 2, '.', ' ') }} ₽
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">
                                    Нет записей по выбранным фильтрам
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/PageQueryParam.js') }}"></script>
@stop
