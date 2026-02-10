@extends('adminlte::page')

@section('title', $title)
@section('content_header')
    <h1>{{ $title }}</h1>
@stop

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <a href="{{ route('payments.create') }}" class="btn btn-primary mb-3">Создать платёж</a>
                    </div>

                    <div class="col-md-3">
                        <select name="user_id" id="user_id"
                                onchange="updatePageWithQueryParam(this)"
                                class="form-control mb-3">
                            <option value="">Все клиенты</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" @if($filters['user_id'] == $client->id) selected @endif>
                                    {{ $client->last_name ?? '' }} {{ $client->name ?? '' }} {{ $client->patronymic ?? '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select name="group_id" id="group_id"
                                onchange="updatePageWithQueryParam(this)"
                                class="form-control mb-3">
                            <option value="">Все группы</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}" @if($filters['group_id'] == $group->id) selected @endif>
                                    {{ $group->title }} ({{ $group->course?->title ?? 'Без курса' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <a href="{{ route('payments.index') }}" class="btn btn-secondary mb-3">Сбросить</a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                        <tr>
                            <th style="width: 50px">#</th>
                            <th>Дата</th>
                            <th>Студент</th>
                            <th>Группа</th>
                            <th>Курс</th>
                            <th>Сумма</th>
                            <th>Чек</th>
                            <th>№ платежа</th>
                            <th style="width: 100px">Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td>{{ $payment->id }}</td>
                                <td>{{ $payment->payment_date->format('d.m.Y') }}</td>
                                <td>
                                    {{ $payment->user->last_name ?? '' }}
                                    {{ $payment->user->name ?? '' }}
                                    {{ $payment->user->patronymic ?? '' }}
                                </td>
                                <td>{{ $payment->group->title }}</td>
                                <td>{{ $payment->group->course?->title ?? '---' }}</td>
                                <td>{{ number_format($payment->amount, 2, '.', ' ') }} ₽</td>
                                <td>
                                    @if($payment->receipt_path)
                                        <a href="{{ route('payments.download', $payment->id) }}"
                                           class="btn btn-sm btn-info">Скачать</a>
                                    @else
                                        <span class="text-muted">---</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $paymentNumber = $payment->payment_number;
                                        $badgeClass = match($paymentNumber) {
                                            1 => 'badge-success',
                                            2 => 'badge-warning',
                                            3 => 'badge-danger',
                                            default => 'badge-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ $paymentNumber }}/3
                                    </span>
                                </td>
                                <td>
                                    @can('update', $payment)
                                        <a href="{{ route('payments.edit', $payment) }}"
                                           class="btn btn-sm btn-warning">Редактировать</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">Нет платежей</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/PageQueryParam.js') }}"></script>
@stop
