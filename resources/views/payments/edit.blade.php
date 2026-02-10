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

                <form action="{{ route('payments.update', $payment->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('put')

                    <div class="form-group">
                        <label for="user_name">Студент</label>
                        <input type="text" id="user_name" class="form-control" value="{{ $payment->user->last_name ?? '' }} {{ $payment->user->name ?? '' }} {{ $payment->user->patronymic ?? '' }}" disabled>
                    </div>

                    <div class="form-group">
                        <label for="group_id">Группа <span class="text-danger">*</span></label>
                        <select name="group_id" id="group_id" class="form-control select2" required>
                            <option value="">Выберите группу</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}"
                                        data-course="{{ $group->course?->title ?? '' }}"
                                        @if(old('group_id', $payment->group_id) == $group->id) selected @endif>
                                    {{ $group->title }}
                                    @if($group->course)
                                        <small class="text-muted">({{ $group->course->title }})</small>
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="payment_date">Дата платежа <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" id="payment_date"
                               class="form-control"
                               value="{{ old('payment_date', $payment->payment_date->format('Y-m-d')) }}"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="amount">Сумма (₽) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" id="amount"
                               class="form-control"
                               step="0.01" min="0.01" max="999999.99"
                               placeholder="0.00"
                               value="{{ old('amount', $payment->amount) }}"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Текущий чек</label>
                        @if($payment->receipt_path)
                            <div>
                                <a href="{{ route('payments.download', $payment->id) }}"
                                   class="btn btn-sm btn-info" target="_blank">Скачать текущий</a>
                                <span class="text-muted">{{ $payment->receipt_name }}</span>
                            </div>
                        @else
                            <p class="text-muted">Чек не загружен</p>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="receipt">Загрузить новый чек</label>
                        <input type="file" name="receipt" id="receipt"
                               class="form-control-file"
                               accept=".jpg,.jpeg,.png,.pdf">
                        <small class="form-text text-muted">
                            Допустимые форматы: JPG, JPEG, PNG, PDF (макс. 50 МБ).
                            Если файл не выбран, текущий чек останется без изменений.
                        </small>
                    </div>

                    <div class="form-group">
                        <a href="{{ route('payments.index') }}" class="btn btn-secondary">Отмена</a>
                        <button type="submit" class="btn btn-success">Сохранить</button>
                        <button type="button" class="btn btn-danger float-right" onclick="confirmDelete()">
                            Удалить платеж
                        </button>
                    </div>
                </form>

                <form id="deleteForm" action="{{ route('payments.destroy', $payment->id) }}" method="POST" style="display: none;">
                    @csrf
                    @method('delete')
                </form>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/i18n/ru.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                language: 'ru',
                width: '100%'
            });

            // Восстанавливаем выбранную группу после ошибки валидации
            const oldGroupId = '{{ old('group_id') }}';
            if (oldGroupId) {
                $('#group_id').val(oldGroupId).trigger('change');
            }
        });

        function confirmDelete() {
            if (confirm('Вы уверены, что хотите удалить этот платёж? Это действие нельзя отменить.')) {
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
@stop
