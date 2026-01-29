@extends('adminlte::page')

@section('title', $title)
@section('content_header')
    <h1>{{ $title }}</h1>
@stop

@section('content')
    <div class="col-md-6">
        <div class="card">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card-body">
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('put')
                    <div class="form-group">
                        <label for="last_name">Фамилия</label>
                        <input type="text" name="last_name" class="form-control" placeholder="Фамилия" value="{{ $user->last_name }}" required>
                    </div>
                    <div class="form-group">
                        <label for="name">Имя</label>
                        <input type="text" name="name" class="form-control" placeholder="Имя" value="{{ $user->name }}" required>
                    </div>
                    <div class="form-group">
                        <label for="patronymic">Отчество</label>
                        <input type="text" name="patronymic" class="form-control" placeholder="Отчество" value="{{ $user->patronymic }}">
                    </div>
                    <div class="form-group">
                        <label for="email">Почта</label>
                        <input type="email" name="email" class="form-control" placeholder="Почта" value="{{ $user->email }}" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Телефон</label>
                        <input type="text" name="phone" class="form-control" placeholder="Телефон" value="{{ $user->phone }}">
                    </div>
                    <div class="form-group">
                        <label for="password">Пароль (оставьте пустым, чтобы не менять)</label>
                        <input type="password" name="password" class="form-control" placeholder="Новый пароль">
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Подтверждение пароля</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Подтверждение пароля">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">Сохранить</button>
                        <a href="{{ $user->isClient() ? route('users.clients') : route('users.employees') }}" class="btn btn-secondary">Отмена</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('css')

@stop

@section('js')

@stop
