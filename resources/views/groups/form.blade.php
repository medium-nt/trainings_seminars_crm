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
        @if(isset($group))
            @method('PUT')
        @endif

        <div class="form-group">
            <label for="title">Название</label>
            <input type="text" name="title" class="form-control" placeholder="Название группы"
                   value="{{ old('title', $group['title'] ?? '') }}" required>
        </div>

        <div class="form-group">
            <label for="course_id">Курс</label>
            <select name="course_id" class="form-control" required>
                <option value="" @selected(old('course_id', $group['course_id'] ?? '') === '')>---</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}"
                        @selected(old('course_id', $group['course_id'] ?? '') == $course->id)>
                        {{ $course->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="teacher_id">Преподаватель</label>
            <select name="teacher_id" class="form-control">
                <option value="" @selected(old('teacher_id', $group['teacher_id'] ?? '') === '')>---</option>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}"
                        @selected(old('teacher_id', $group['teacher_id'] ?? '') == $teacher->id)>
                        {{ $teacher->full_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="start_date">Дата начала</label>
                    <input type="date" name="start_date" class="form-control"
                           value="{{ old('start_date', isset($group) && $group->start_date ? $group->start_date->format('Y-m-d') : '') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="end_date">Дата окончания</label>
                    <input type="date" name="end_date" class="form-control"
                           value="{{ old('end_date', isset($group) && $group->end_date ? $group->end_date->format('Y-m-d') : '') }}">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="note">Заметка</label>
            <textarea name="note" class="form-control" rows="3" placeholder="Заметка">{{ old('note', $group['note'] ?? '') }}</textarea>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-success">{{ $buttonText }}</button>
        </div>
    </form>
</div>
