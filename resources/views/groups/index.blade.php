@extends('adminlte::page')

@section('title', $title)
@section('content_header')
    <h1>{{ $title }}</h1>
@stop

@section('content')
    <div class="col-md-12 col-lg-12">
        <div class="card">
            <div class="card-body">

                <a href="{{ route('groups.create') }}" class="btn btn-primary mb-3">Добавить</a>

                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                    <tr>
                        <th style="width: 50px">#</th>
                        <th>Название</th>
                        <th>Курс</th>
                        <th>Преподаватель</th>
                        <th>Даты</th>
                        <th>Статус</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($groups as $group)
                        <tr>
                            <td>{{ $group->id }}</td>
                            <td>{{ $group->title }}</td>
                            <td>{{ $group->course?->title ?? '---' }}</td>
                            <td>{{ $group->teacher?->full_name ?? '---' }}</td>
                            <td>
                                {{ $group->start_date?->format('d.m.Y') ?? '---' }}
                                @if($group->end_date)
                                    - {{ $group->end_date->format('d.m.Y') }}
                                @endif
                            </td>
                            <td>{{ $group->status ?? '---' }}</td>
                            <td>
                                <a href="{{ route('groups.edit', $group->id) }}" class="btn btn-primary">Редактировать</a>
                                <form action="{{ route('groups.destroy', $group->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Удалить группу?')">Удалить</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                {{ $groups->links() }}
            </div>
        </div>
    </div>
@endsection

@section('css')

@stop

@section('js')

@stop
