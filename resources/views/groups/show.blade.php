@extends('adminlte::page')

@section('title', $title)
@section('content_header')
    <h1>{{ $title }}</h1>
@stop

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <a href="{{ route('groups.index') }}" class="btn btn-secondary mb-3">Назад</a>
                <a href="{{ route('groups.edit', $group->id) }}" class="btn btn-primary mb-3">Редактировать</a>
                <a href="{{ route('groups.clients.create', $group->id) }}" class="btn btn-success mb-3">Добавить слушателей</a>
                <a href="{{ route('schedules.index', ['group_id' => $group->id]) }}" class="btn btn-info mb-3">Расписание</a>

                <div class="row">
                    <div class="col-md-6">
                        <h5>Информация о группе</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 40%;">Название</th>
                                <td>{{ $group->title }}</td>
                            </tr>
                            <tr>
                                <th>Курс</th>
                                <td>{{ $group->course?->title ?? '---' }}</td>
                            </tr>
                            <tr>
                                <th>Преподаватель</th>
                                <td>{{ $group->teacher?->full_name ?? '---' }}</td>
                            </tr>
                            <tr>
                                <th>Даты</th>
                                <td>
                                    {{ $group->start_date?->format('d.m.Y') ?? '---' }}
                                    @if($group->end_date)
                                        - {{ $group->end_date->format('d.m.Y') }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Статус</th>
                                <td>{{ $group->status ?? '---' }}</td>
                            </tr>
                            <tr>
                                <th>Заметка</th>
                                <td>{{ $group->note ?? '---' }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <h5>Слушатели ({{ $group->clients->count() }})</h5>

                        @if($group->clients->count() > 0)
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>ФИО</th>
                                    <th>Email</th>
                                    <th>Добавлен</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($group->clients as $client)
                                    <tr>
                                        <td>{{ $client->full_name }}</td>
                                        <td>{{ $client->email }}</td>
                                        <td>{{ $client->pivot->created_at?->format('d.m.Y') ?? '---' }}</td>
                                        <td>
                                            <form action="{{ route('groups.clients.destroy', [$group->id, $client->id]) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Удалить слушателя из группы?')">✕</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-muted">В группе пока нет слушателей</p>
                            <a href="{{ route('groups.clients.create', $group->id) }}" class="btn btn-success btn-sm">Добавить первого</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')

@stop

@section('js')

@stop
