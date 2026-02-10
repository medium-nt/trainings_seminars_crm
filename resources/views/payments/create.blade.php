@extends('adminlte::page')

@section('title', $title)
@section('content_header')
    <h1>{{ $title }}</h1>
@stop

@section('content')
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('payments.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label for="user_id">Студент <span class="text-danger">*</span></label>
                        <select name="user_id" id="user_id" class="form-control select2" required>
                            <option value="">Выберите студента</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}"
                                        data-groups="{{ $client->studentGroups->pluck('id')->implode(',') }}">
                                    {{ $client->last_name ?? '' }}
                                    {{ $client->name ?? '' }}
                                    {{ $client->patronymic ?? '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="group_id">Группа <span class="text-danger">*</span></label>
                        <select name="group_id" id="group_id" class="form-control select2" required>
                            <option value="">Выберите группу</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}"
                                        data-course="{{ $group->course?->title ?? '' }}"
                                        data-students="{{ $group->clients->pluck('id')->implode(',') }}">
                                    {{ $group->title }}
                                    @if($group->course)
                                        <small class="text-muted">({{ $group->course->title }})</small>
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <small id="payment_info" class="form-text text-info"></small>
                    </div>

                    <div class="form-group">
                        <label for="payment_date">Дата платежа <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" id="payment_date"
                               class="form-control"
                               value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="amount">Сумма (₽) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" id="amount"
                               class="form-control"
                               step="0.01" min="0.01" max="999999.99"
                               placeholder="0.00"
                               value="{{ old('amount') }}"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="receipt">Чек</label>
                        <input type="file" name="receipt" id="receipt"
                               class="form-control-file"
                               accept=".jpg,.jpeg,.png,.pdf">
                        <small class="form-text text-muted">
                            Допустимые форматы: JPG, JPEG, PNG, PDF (макс. 50 МБ)
                        </small>
                    </div>

                    <div class="form-group">
                        <a href="{{ route('payments.index') }}" class="btn btn-secondary">Отмена</a>
                        <button type="submit" id="submitBtn" class="btn btn-primary">Создать</button>
                    </div>
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
            const userSelect = $('#user_id');
            const groupSelect = $('#group_id');
            const paymentInfo = $('#payment_info');
            const submitBtn = $('#submitBtn');

            // Данные о количестве платежей для каждой пары студент-группа
            const paymentCounts = @js($paymentCounts ?? []);

            // Сохраняем исходные данные опций
            const originalGroupOptions = groupSelect.html();

            userSelect.select2({
                theme: 'bootstrap4',
                language: 'ru',
                width: '100%'
            });

            groupSelect.select2({
                theme: 'bootstrap4',
                language: 'ru',
                width: '100%'
            });

            // Функция для фильтрации групп по студенту
            function filterGroupsByUser(userId) {
                // Восстанавливаем все группы
                groupSelect.html(originalGroupOptions);
                groupSelect.val(null).trigger('change');
                paymentInfo.text('');

                if (userId) {
                    // Фильтруем группы - оставляем только те, где учится студент
                    groupSelect.find('option').each(function() {
                        const option = $(this);
                        const groupStudents = option.data('students');

                        // Если нет студентов или студент не в списке - удаляем группу
                        if (!groupStudents || groupStudents.toString().length === 0 || !groupStudents.toString().includes(userId)) {
                            if (option.val() !== '') {
                                option.remove();
                            }
                        }
                    });
                }

                // Пересоздаём Select2 после изменения опций
                groupSelect.trigger('change.select2');
            }

            // Функция для обновления информации о платежах
            function updatePaymentInfo() {
                const userId = userSelect.val();
                const groupId = groupSelect.val();

                if (userId && groupId && paymentCounts[userId] && paymentCounts[userId][groupId]) {
                    const count = paymentCounts[userId][groupId];
                    const remaining = 3 - count;
                    if (remaining > 0) {
                        paymentInfo.text('Создано платежей: ' + count + '/3 (осталось: ' + remaining + ')').removeClass('text-danger').addClass('text-info');
                        submitBtn.show().prop('disabled', false);
                    } else {
                        paymentInfo.text('Создано платежей: ' + count + '/3').removeClass('text-info').addClass('text-danger');
                        submitBtn.hide().prop('disabled', true);
                    }
                } else if (userId && groupId) {
                    paymentInfo.text('Создано платежей: 0/3').removeClass('text-danger').addClass('text-info');
                    submitBtn.show().prop('disabled', false);
                } else {
                    paymentInfo.text('');
                    submitBtn.show().prop('disabled', false);
                }
            }

            // При выборе студента - фильтруем группы
            userSelect.on('change', function() {
                filterGroupsByUser($(this).val());
            });

            // Показываем количество платежей при выборе группы
            groupSelect.on('change', function() {
                updatePaymentInfo();
            });

            // Восстанавливаем состояние после ошибки валидации
            @if(old('user_id'))
                userSelect.val('{{ old('user_id') }}').trigger('change');
                @if(old('group_id'))
                    setTimeout(function() {
                        groupSelect.val('{{ old('group_id') }}').trigger('change');
                    }, 100);
                @endif
            @else
                // При первой загрузке скрываем все группы
                groupSelect.find('option:not([value=""])').remove();
                groupSelect.trigger('change.select2');
            @endif
        });
    </script>
@stop
