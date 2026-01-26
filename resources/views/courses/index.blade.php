@extends('adminlte::page')

@section('title', $title)
@section('content_header')
    <h1>{{ $title }}</h1>
@stop

@section('content')
    <div class="col-md-12 col-lg-6">
        <div class="card">
            <div class="card-body">

                <a href="{{ route('courses.create') }}" class="btn btn-primary mb-3">Добавить</a>

                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                    <tr>
                        <th style="width: 50px">#</th>
                        <th>Название</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($courses as $course)
                        <tr>
                            <td>{{ $course->id }}</td>
                            <td>{{ $course->title }}</td>
                            <td>
                                <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-primary">Редактировать</a>
                                <form action="{{ route('courses.destroy', $course->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Удалить курс?')">Удалить</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <div class="d-flex justify-content-center mt-3">
                    {{ $courses->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')

@stop

@section('js')

@stop
