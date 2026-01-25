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
    <form action="{{ $action }}" method="POST">
        @csrf
        @if(isset($schedule))
            @method('PUT')
        @endif

        <div class="form-group">
            <label for="group_id">Группа</label>
            <select name="group_id" id="groupInput" class="form-control" required>
                <option value="">---</option>
                @foreach($groups as $group)
                    <option value="{{ $group->id }}" data-id="{{ $group->id }}">
                        {{ $group->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="start">Дата и время начала</label>
                    <input type="datetime-local" name="start" id="startInput" class="form-control" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="end">Дата и время окончания</label>
                    <input type="datetime-local" name="end" id="endInput" class="form-control" required>
                </div>
            </div>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-success">{{ $buttonText }}</button>
            <a href="{{ route('schedules.index') }}" class="btn btn-secondary">Отмена</a>
        </div>
    </form>
</div>

@php
    // Подготовка данных для JavaScript
    $groupId = old('group_id', isset($schedule) ? $schedule->group_id : request()->get('group_id', ''));
    $startValue = '';
    $endValue = '';

    if (isset($schedule) && $schedule->start) {
        $startValue = $schedule->start->format('Y-m-d\TH:i');
    }
    if (isset($schedule) && $schedule->end) {
        $endValue = $schedule->end->format('Y-m-d\TH:i');
    }

    // Если есть query параметры для start/end
    if (request()->has('start') && request()->get('start')) {
        $startValue = \Carbon\Carbon::parse(request()->get('start'))->format('Y-m-d\TH:i');
    }
    if (request()->has('end') && request()->get('end')) {
        $endValue = \Carbon\Carbon::parse(request()->get('end'))->format('Y-m-d\TH:i');
    }
@endphp

<script>
    // Установка группы
    const groupId = '{{ $groupId }}';
    if (groupId) {
        const groupSelect = document.getElementById('groupInput');
        const groupOption = groupSelect.querySelector('option[data-id="' + groupId + '"]');
        if (groupOption) {
            groupOption.selected = true;
        }
    }

    // Установка даты и времени
    document.getElementById('startInput').value = '{{ $startValue }}';
    document.getElementById('endInput').value = '{{ $endValue }}';
</script>
