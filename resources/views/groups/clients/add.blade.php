@extends('adminlte::page')

@section('title', $title)
@section('content_header')
    <h1>{{ $title }}</h1>
@stop

@section('content')
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('groups.clients.store', $group->id) }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="clients">Слушатели</label>
                        <select name="clients[]" id="clients" class="form-control" multiple style="width: 100%;">
                        </select>
                        <small class="form-text text-muted">Начните вводить имя для поиска</small>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-success">Добавить выбранных</button>
                        <a href="{{ route('groups.show', $group->id) }}" class="btn btn-secondary">Отмена</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#clients').select2({
                ajax: {
                    url: '{{ route('clients.search') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            search: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.full_name + ' (' + item.email + ')'
                                };
                            })
                        };
                    },
                    cache: true
                },
                placeholder: 'Поиск слушателей...',
                minimumInputLength: 2,
                language: {
                    inputTooShort: function() {
                        return 'Введите минимум 2 символа для поиска';
                    },
                    searching: function() {
                        return 'Поиск...';
                    },
                    noResults: function() {
                        return 'Ничего не найдено';
                    }
                }
            });
        });
    </script>
@stop
