@extends('adminlte::page')

@section('title', $title)
@section('content_header')
    <h1>{{ $title }}</h1>
@stop

@section('content')
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="group_id">Группа</label>
                    <select name="group_id" id="group_id" class="form-control"
                            onchange="updatePageWithQueryParam(this)">
                        <option value="">Все группы</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ $selectedGroup == $group->id ? 'selected' : '' }}>
                                {{ $group->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="show_only_pending">Статус документов</label>
                    <select name="show_only_pending" id="show_only_pending" class="form-control"
                            onchange="updatePageWithQueryParam(this)">
                        <option value="">Все клиенты</option>
                        <option value="1" {{ $showOnlyPending ? 'selected' : '' }}>
                            Только с непроверенными
                        </option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead>
                        <tr>
                            <th>ФИО Клиента</th>
                            @foreach($documentTypes as $title)
                                <th class="text-center">{{ $title }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @if($statistics->count() > 0)
                            @foreach($statistics as $row)
                                <tr>
                                    <td class="py-2">
                                        <a href="{{ route('users.edit', $row['id']) }}"
                                           target="_blank"
                                           class="text-decoration-none">
                                            {{ $row['full_name'] }}
                                        </a>
                                    </td>
                                    @foreach($documentTypes as $type => $title)
                                        <td class="text-center py-2">
                                            @if(!$row['documents'][$type]['exists'])
                                                <span class="fas fa-times text-danger fa-lg"></span>
                                            @elseif(!$row['documents'][$type]['all_approved'])
                                                <span class="fas fa-check text-secondary fa-lg"></span>
                                            @else
                                                <span class="fas fa-check text-success fa-lg"></span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="{{ count($documentTypes) + 1 }}" class="text-center text-muted py-3">
                                    Нет клиентов по выбранным фильтрам
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/PageQueryParam.js') }}"></script>
@stop
