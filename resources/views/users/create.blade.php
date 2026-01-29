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
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    @if(auth()->user()->isAdmin())
                        <div class="form-group">
                            <label for="role_id">Роль</label>
                            <select name="role_id" class="form-control" required>
                                <option value="" selected disabled>---</option>
                                <option value="1" @selected(old('role_id') == 1)>Клиент</option>
                                <option value="2" @selected(old('role_id') == 2)>Менеджер</option>
                                <option value="4" @selected(old('role_id') == 4)>Преподаватель</option>
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="role_id" value="1">
                    @endif
                    <div class="form-group">
                        <label for="last_name">Фамилия</label>
                        <input type="text" name="last_name" class="form-control" placeholder="Фамилия" value="{{ old('last_name') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="name">Имя</label>
                        <input type="text" name="name" class="form-control" placeholder="Имя" value="{{ old('name') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="patronymic">Отчество</label>
                        <input type="text" name="patronymic" class="form-control" placeholder="Отчество" value="{{ old('patronymic') }}">
                    </div>
                    <div class="form-group">
                        <label for="email">Почта</label>
                        <input type="email" name="email" class="form-control" placeholder="Почта" value="{{ old('email') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Телефон</label>
                        <input type="text" name="phone" class="form-control" placeholder="Телефон" value="{{ old('phone') }}">
                    </div>
                    <div class="form-group">
                        <label for="password">Пароль</label>
                        <input type="password" name="password" class="form-control" placeholder="Пароль" value="{{ old('password') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Подтверждение пароля</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Подтверждение пароля" value="{{ old('password_confirmation') }}" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">Добавить</button>
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
