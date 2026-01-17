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
                        <th>Фамилия</th>
                        <th>Почта</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->last_name }}</td>
                            <td>{{ $user->email }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('css')

@stop

@section('js')

@stop
