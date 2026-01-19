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

                <form action="{{ route('profile.update') }}" method="POST">
                    @method('PUT')
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="last_name">Фамилия</label>
                            <input type="text" class="form-control" id="last_name" value="{{ $user->last_name }}"
                                   name="last_name" placeholder="Фамилия" required>
                        </div>

                        <div class="form-group">
                            <label for="name">Имя</label>
                            <input type="text" class="form-control" id="name" value="{{ $user->name }}"
                                   name="name" placeholder="Имя" required>
                        </div>

                        <div class="form-group">
                            <label for="patronymic">Отчество</label>
                            <input type="text" class="form-control" id="patronymic" value="{{ $user->patronymic }}"
                                   name="patronymic" placeholder="Отчество">
                        </div>

                        <div class="form-group">
                            <label for="phone">Телефон</label>
                            <input type="text" class="form-control" id="phone" value="{{ $user->phone }}"
                                   name="phone" placeholder="Телефон" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" value="{{ $user->email }}"
                                   name="email" placeholder="Email" required>
                        </div>

                        <hr class="my-4">

                        <div class="form-group">
                            <label for="password">Новый пароль</label>
                            <input type="password" class="form-control" id="password" name="password"
                                   placeholder="Пароль">
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Подтверждение пароля</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                   name="password_confirmation" placeholder="Подтверждение пароля">
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Сохранить</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
@endsection

@section('css')

@stop

@section('js')

@stop
