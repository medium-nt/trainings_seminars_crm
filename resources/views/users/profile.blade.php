@extends('adminlte::page')

@section('title', $title)
@section('content_header')
    <h1>{{ $title }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Личные данные</h3>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('profile.update') }}" method="POST">
                    @method('PUT')
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="last_name">Фамилия</label>
                            <input type="text" class="form-control" id="last_name" value="{{ $user->last_name }}"
                                   name="last_name" placeholder="Фамилия" required>
                        </div>

                        <div class="form-group">
                            <label for="name">Имя</label>
                            <input type="text" class="form-control" id="name" value="{{ $user->name }}"
                                   name="name" placeholder="Имя" required>
                        </div>

                        <div class="form-group">
                            <label for="patronymic">Отчество</label>
                            <input type="text" class="form-control" id="patronymic" value="{{ $user->patronymic }}"
                                   name="patronymic" placeholder="Отчество">
                        </div>

                        <div class="form-group">
                            <label for="phone">Телефон</label>
                            <input type="text" class="form-control" id="phone" value="{{ $user->phone }}"
                                   name="phone" placeholder="Телефон" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" value="{{ $user->email }}"
                                   name="email" placeholder="Email" required>
                        </div>

                        <hr class="my-4">

                        <div class="form-group">
                            <label for="password">Новый пароль</label>
                            <input type="password" class="form-control" id="password" name="password"
                                   placeholder="Пароль">
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Подтверждение пароля</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                   name="password_confirmation" placeholder="Подтверждение пароля">
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Сохранить</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Блок оплаты (только для клиентов с группами) -->
            @if(auth()->user()->isClient() && $groups->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Оплата</h3>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                            <tr>
                                <th>Группа</th>
                                <th>Стоимость</th>
                                <th>Оплачено</th>
                                <th>Остаток</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($groups as $group)
                                <tr>
                                    <td>{{ $group['title'] }}</td>
                                    <td>{{ number_format($group['price'], 2, '.', ' ') }} ₽</td>
                                    <td>{{ number_format($group['paid'], 2, '.', ' ') }} ₽</td>
                                    <td class="{{ $group['remaining'] > 0 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format($group['remaining'], 2, '.', ' ') }} ₽
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>

        <!-- Блок документов (только для клиентов) -->
        @if(auth()->user()->isClient())
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Мои документы</h3>
                    </div>
                <div class="card-body">
                    @foreach($documentTypes as $docType)
                        <div class="document-type-block mb-4 p-3 border rounded">
                            <h5>{{ $docType['title'] }}</h5>

                            <form action="{{ route('documents.store') }}" method="POST"
                                  enctype="multipart/form-data" class="upload-form mt-2">
                                @csrf
                                <input type="hidden" name="type" value="{{ $docType['type'] }}">
                                <div class="row g-2">
                                    <div class="col-12 col-sm">
                                        <input type="file" name="files[]" class="form-control"
                                               accept=".pdf" multiple required>
                                    </div>
                                    <div class="col-12 col-sm-auto">
                                        <button type="submit" class="btn btn-primary w-100">Загрузить</button>
                                    </div>
                                </div>
                            </form>

                            <div class="documents-list mt-3">
                                @foreach($documents->where('type', $docType['type']) as $doc)
                                    @include('partials.document-item', ['document' => $doc])
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('css')

@stop

@section('js')
    <!-- Модальное окно для предпросмотра изображений -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">Предпросмотр изображения</h5>
                    <button type="button" class="close btn btn-danger btn-sm" onclick="closeImagePreview()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center bg-dark p-0">
                    <img id="previewImage" src="" alt="Предпросмотр" class="img-fluid mx-auto" style="max-height: 75vh;">
                </div>
            </div>
        </div>
    </div>

    <style>
        #imagePreviewModal .close {
            font-size: 24px;
            opacity: 1;
        }
        #imagePreviewModal .modal-body {
            padding: 0;
        }
    </style>

    <script>
        function showImagePreview(imageUrl) {
            document.getElementById('previewImage').src = imageUrl;
            $('#imagePreviewModal').modal('show');
        }

        function closeImagePreview() {
            $('#imagePreviewModal').modal('hide');
        }

        // Закрытие по клику на затемнённый фон
        $('#imagePreviewModal').on('click', function(e) {
            if (e.target === this) {
                closeImagePreview();
            }
        });

        // Закрытие по Escape
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $('#imagePreviewModal').hasClass('show')) {
                closeImagePreview();
            }
        });
    </script>
@stop
