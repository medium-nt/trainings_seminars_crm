@extends('adminlte::page')

@section('title', $title)
@section('content_header')
    <h1>{{ $title }}</h1>
@stop

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <a href="{{ route('users.create') }}" class="btn btn-primary mb-3">Добавить</a>
                    </div>

                    <div class="col-md-3">
                        <select name="group_id" id="group_id"
                                onchange="updatePageWithQueryParam(this)"
                                class="form-control mb-3">
                            <option value="all">Все группы</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}" @if(request('group_id') == $group->id) selected @endif>{{ $group->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <form action="{{ route('users.clients') }}" method="get" class="form-inline">
                            <input type="text" name="search" class="form-control mr-2 mb-2" placeholder="Поиск" value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary mb-2">Найти</button>
                        </form>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('users.clients') }}" class="btn btn-secondary mb-3">Сбросить фильтр</a>
                    </div>
                </div>

                <div class="table-responsive">
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
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/PageQueryParam.js') }}"></script>
@stop
