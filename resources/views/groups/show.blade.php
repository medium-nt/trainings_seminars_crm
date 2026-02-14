@extends('adminlte::page')

@section('title', $title)
@section('content_header')
    <h1>{{ $title }}</h1>
@stop

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <a href="{{ route('groups.index') }}" class="btn btn-secondary mb-3">Назад</a>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('groups.edit', $group->id) }}" class="btn btn-primary mb-3">Редактировать</a>
                @endif
                <a href="{{ route('groups.clients.create', $group->id) }}" class="btn btn-success mb-3">Добавить слушателей</a>
{{--                <a href="{{ route('schedules.index', ['group_id' => $group->id]) }}" class="btn btn-info mb-3">Расписание</a>--}}

                <div class="row">
                    <div class="col-md-6">
                        <h5>Информация о группе</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 40%;">Название</th>
                                <td>{{ $group->title }}</td>
                            </tr>
                            <tr>
                                <th>Курс</th>
                                <td>{{ $group->course?->title ?? '---' }}</td>
                            </tr>
                            <tr>
                                <th>Стоимость</th>
                                <td>{{ number_format($group->price, 2, '.', ' ') }} ₽</td>
                            </tr>
                            <tr>
                                <th>Преподаватель</th>
                                <td>{{ $group->teacher?->full_name ?? '---' }}</td>
                            </tr>
                            <tr>
                                <th>Даты</th>
                                <td>
                                    {{ $group->start_date?->format('d.m.Y') ?? '---' }}
                                    @if($group->end_date)
                                        - {{ $group->end_date->format('d.m.Y') }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Статус</th>
                                <td>{{ $group->status ?? '---' }}</td>
                            </tr>
                            <tr>
                                <th>Заметка</th>
                                <td>{{ $group->note ?? '---' }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <h5>Слушатели ({{ $group->clients->count() }})</h5>

                        @if($group->clients->count() > 0)
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>ФИО</th>
                                    <th>Email</th>
                                    <th>Стоимость</th>
                                    <th>Добавлен</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($group->clients as $client)
                                    <tr data-client-id="{{ $client->id }}" data-current-price="{{ $client->pivot->price ?? '' }}">
                                        <td>
                                            <a href="{{ route('users.edit', $client->id) }}" class="text-primary" target="_blank">
                                                {{ $client->full_name }}
                                            </a></td>
                                        <td>{{ $client->email }}</td>
                                        <td class="price-cell">
                                            <button type="button"
                                                    class="btn btn-secondary btn-sm btn-edit-price mr-1"
                                                    title="Редактировать цену">
                                                ✎
                                            </button>
                                            <span class="price-display">
                                                {{ $client->pivot->price ? number_format($client->pivot->price, 2, '.', ' ') . ' ₽' : '---' }}
                                            </span>
                                            <input type="number"
                                                   step="0.01"
                                                   min="0"
                                                   max="99999999"
                                                   class="form-control form-control-sm price-input d-none"
                                                   style="width: 80px; display: inline-block;"
                                                   value="{{ $client->pivot->price ?? '' }}"
                                                   placeholder="0.00">
                                            <button type="button"
                                                    class="btn btn-info btn-sm btn-save-price d-none ml-1"
                                                    title="Сохранить цену">
                                                ✓
                                            </button>
                                        </td>
                                        <td>{{ $client->pivot->created_at?->format('d.m.Y') ?? '---' }}</td>
                                        <td>
                                            <form action="{{ route('groups.clients.destroy', [$group->id, $client->id]) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Удалить слушателя из группы?')">✕</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-muted">В группе пока нет слушателей</p>
                            <a href="{{ route('groups.clients.create', $group->id) }}" class="btn btn-success btn-sm">Добавить первого</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')

@stop

@section('js')
    <script>
    (function() {
        // Inline-редактирование
        document.querySelectorAll('.btn-edit-price').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const row = this.closest('tr');
                const priceInput = row.querySelector('.price-input');
                const priceDisplay = row.querySelector('.price-display');
                const saveBtn = row.querySelector('.btn-save-price');

                priceInput.classList.remove('d-none');
                priceDisplay.classList.add('d-none');
                this.classList.add('d-none');
                saveBtn.classList.remove('d-none');

                priceInput.focus();
            });
        });

        document.querySelectorAll('.btn-save-price').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const row = this.closest('tr');
                const clientId = row.dataset.clientId;
                const priceInput = row.querySelector('.price-input');
                const price = priceInput.value;

                savePriceInline(clientId, price, row);
            });
        });

        document.querySelectorAll('.price-input').forEach(input => {
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const row = this.closest('tr');
                    const saveBtn = row.querySelector('.btn-save-price');
                    saveBtn.click();
                }
                if (e.key === 'Escape') {
                    const row = this.closest('tr');
                    this.value = row.dataset.currentPrice || '';
                    this.blur();
                }
            });

            input.addEventListener('blur', function() {
                const row = this.closest('tr');
                const saveBtn = row.querySelector('.btn-save-price');

                if (!saveBtn.classList.contains('d-none')) {
                    return;
                }

                this.classList.add('d-none');
                row.querySelector('.price-display').classList.remove('d-none');
                row.querySelector('.btn-edit-price').classList.remove('d-none');
            });
        });

        function savePriceInline(clientId, price, row) {
            const btn = row.querySelector('.btn-save-price');
            const originalText = btn.innerHTML;
            const url = '/groups/' + {{ $group->id }} + '/clients/' + clientId + '/price';
            btn.disabled = true;
            btn.innerHTML = '...';

            $.ajax({
                url: url,
                method: 'PATCH',
                data: JSON.stringify({ price: price }),
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data) {
                    if (data.success) {
                        row.querySelector('.price-display').textContent = data.formatted;
                        row.dataset.currentPrice = price || '';

                        const priceInput = row.querySelector('.price-input');
                        const priceDisplay = row.querySelector('.price-display');
                        const editBtn = row.querySelector('.btn-edit-price');

                        priceInput.classList.add('d-none');
                        priceDisplay.classList.remove('d-none');
                        editBtn.classList.remove('d-none');
                        btn.classList.add('d-none');
                    }
                },
                error: function(xhr) {
                    alert('Ошибка при сохранении: HTTP ' + xhr.status);
                },
                complete: function() {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            });
        }
    })();
    </script>
@stop
