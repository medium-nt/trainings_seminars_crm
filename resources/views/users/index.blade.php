@extends('adminlte::page')

@section('title', $title)
@section('content_header')
    <h1>{{ $title }}</h1>
@stop

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">

                <a href="{{ route('users.create') }}" class="btn btn-primary mb-3">Добавить</a>

                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                    <tr>
                        <th style="width: 50px">#</th>
                        <th>Имя</th>
                        <th>Почта</th>
                        <th>Телефон</th>
                        <th>Роль</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->last_name ?? '' }} {{ $user->name ?? '' }} {{ $user->patronymic ?? '' }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone }}</td>
                            <td>{{ $user->role_name }}</td>
                            <td>
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary">Редактировать</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <div class="d-flex justify-content-center mt-3">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')

@stop

@section('js')

@stop
