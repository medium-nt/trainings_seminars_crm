@extends('adminlte::page')

@section('title', $title)
@section('content_header')
    <h1>{{ $title }}</h1>
@stop

@section('content')
    <div class="col-md-6">
        <div class="card">
            @include('courses.form', [
                'action' => route('courses.store'),
                'buttonText' => 'Добавить'
            ])
        </div>
    </div>
@endsection

@section('css')

@stop

@section('js')

@stop
