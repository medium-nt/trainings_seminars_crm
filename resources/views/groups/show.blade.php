@extends('adminlte::page')

@section('title', $title)
@section('content_header')
    <h1>{{ $title }}</h1>
@stop

@section('content')
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered mb-3">
                    <tr>
                        <th style="width: 30%;">ID</th>
                        <td>{{ $group->id }}</td>
                    </tr>
                    <tr>
                        <th>Название</th>
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
                        <th>Дата начала</th>
                        <td>{{ $group->start_date?->format('d.m.Y') ?? '---' }}</td>
                    </tr>
                    <tr>
                        <th>Дата окончания</th>
                        <td>{{ $group->end_date?->format('d.m.Y') ?? '---' }}</td>
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

                <a href="{{ route('groups.index') }}" class="btn btn-secondary">Назад</a>
                <a href="{{ route('groups.edit', $group->id) }}" class="btn btn-primary">Редактировать</a>
            </div>
        </div>
    </div>
@endsection

@section('css')

@stop

@section('js')

@stop
