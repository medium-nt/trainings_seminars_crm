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
        @if(isset($course))
            @method('PUT')
        @endif

        <div class="form-group">
            <label for="title">Название</label>
            <input type="text" name="title" class="form-control" placeholder="Название курса"
                   value="{{ old('title', $course->title ?? '') }}" required>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-success">{{ $buttonText }}</button>
        </div>
    </form>
</div>
