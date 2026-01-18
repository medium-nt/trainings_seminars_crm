@extends('adminlte::page')

@section('title', $title)
@section('content_header')
    <h1>{{ $title }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <p>Тестовые пользователи:</p>

            1@1.ru | 111111 - админ
            <br>
            <br>
            2@2.ru | 222222 - менеджер
            <br>
            <br>
            3@3.ru | 333333 - клиент
            <br>
            <br>
            4@4.ru | 444444 - преподаватель
            <br>
        </div>
    </div>
@endsection

@section('css')

@stop

@section('js')

@stop
