@extends('adminlte::page')

@section('title', $title)
@section('content_header')
    <h1>{{ $title }}</h1>
@stop

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <label for="groupFilter">Фильтр по группе:</label>
                    <select id="groupFilter" class="form-control" style="width: 300px;">
                        <option value="">Все группы</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->title }}</option>
                        @endforeach
                    </select>
                    <button id="addScheduleBtn" class="btn btn-success ml-2">Добавить занятие</button>
                </div>

                <div id='calendar'></div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css' rel='stylesheet' />
@stop

@section('js')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/locales/ru.global.min.js'></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const groupFilter = document.getElementById('groupFilter');
        const addScheduleBtn = document.getElementById('addScheduleBtn');

        // Функция для перехода к созданию занятия с группой
        function goToCreate(start = null, end = null) {
            let url = '/schedules/create';
            const params = new URLSearchParams();

            if (groupFilter.value) {
                params.append('group_id', groupFilter.value);
            }

            if (start) {
                // Убираем Z (UTC) и сохраняем локальное время
                const localStart = new Date(start);
                const formattedStart = localStart.getFullYear() + '-' +
                    String(localStart.getMonth() + 1).padStart(2, '0') + '-' +
                    String(localStart.getDate()).padStart(2, '0') + 'T' +
                    String(localStart.getHours()).padStart(2, '0') + ':' +
                    String(localStart.getMinutes()).padStart(2, '0') + ':' +
                    String(localStart.getSeconds()).padStart(2, '0');
                params.append('start', formattedStart);
            }

            if (end) {
                const localEnd = new Date(end);
                const formattedEnd = localEnd.getFullYear() + '-' +
                    String(localEnd.getMonth() + 1).padStart(2, '0') + '-' +
                    String(localEnd.getDate()).padStart(2, '0') + 'T' +
                    String(localEnd.getHours()).padStart(2, '0') + ':' +
                    String(localEnd.getMinutes()).padStart(2, '0') + ':' +
                    String(localEnd.getSeconds()).padStart(2, '0');
                params.append('end', formattedEnd);
            }

            if (params.toString()) {
                url += '?' + params.toString();
            }

            window.location.href = url;
        }

        // Кнопка "Добавить занятие"
        addScheduleBtn.addEventListener('click', function() {
            goToCreate();
        });

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            locale: 'ru',
            firstDay: 1,
            allDaySlot: false,
            height: 'auto',
            slotMinTime: '07:00',
            slotMaxTime: '22:00',
            events: function(info, successCallback, failureCallback) {
                const groupId = groupFilter.value;
                let url = '{{ route('schedules.api.events') }}';

                if (groupId) {
                    url += '?group_id=' + groupId;
                }

                fetch(url)
                    .then(response => response.json())
                    .then(events => successCallback(events))
                    .catch(error => failureCallback(error));
            },
            eventClick: function(info) {
                window.location.href = '/schedules/' + info.event.id + '/edit';
            },
            selectable: true,
            select: function(info) {
                const start = info.start.toISOString();
                const end = info.end ? info.end.toISOString() : start;
                goToCreate(start, end);
            }
        });

        calendar.render();

        groupFilter.addEventListener('change', function() {
            calendar.refetchEvents();
        });
    });
    </script>
@stop
