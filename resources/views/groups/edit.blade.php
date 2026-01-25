@extends('adminlte::page')

@section('title', $title)
@section('content_header')
    <h1>{{ $title }}</h1>
@stop

@section('content')
    <div class="col-md-8">
        <div class="card">
            @include('groups.form', [
                'action' => route('groups.update', $group->id),
                'buttonText' => 'Сохранить',
                'group' => $group
            ])
        </div>
    </div>
@endsection

@section('css')

@stop

@section('js')

@stop
