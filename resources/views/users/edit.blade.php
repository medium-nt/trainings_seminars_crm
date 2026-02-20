@extends('adminlte::page')

@section('title', $title)
@section('content_header')
    <h1>{{ $title }}</h1>
@stop

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <!-- Форма редактирования пользователя -->
            <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('put')

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Данные пользователя</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="last_name">Фамилия</label>
                            <input type="text" name="last_name" class="form-control" placeholder="Фамилия" value="{{ $user->last_name }}" required>
                        </div>
                        <div class="form-group">
                            <label for="name">Имя</label>
                            <input type="text" name="name" class="form-control" placeholder="Имя" value="{{ $user->name }}" required>
                        </div>
                        <div class="form-group">
                            <label for="patronymic">Отчество</label>
                            <input type="text" name="patronymic" class="form-control" placeholder="Отчество" value="{{ $user->patronymic }}">
                        </div>
                        <div class="form-group">
                            <label for="email">Почта</label>
                            <input type="email" name="email" class="form-control" placeholder="Почта" value="{{ $user->email }}" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Телефон</label>
                            <input type="text" name="phone" class="form-control" placeholder="Телефон" value="{{ $user->phone }}">
                        </div>

                        <div class="form-group">
                            <label for="password">Пароль (оставьте пустым, чтобы не менять)</label>
                            <input type="password" name="password" class="form-control" placeholder="Новый пароль">
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Подтверждение пароля</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Подтверждение пароля">
                        </div>
                    </div>
                </div>

                @if($user->isClient())
                <!-- Кто платит -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Кто платит</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Кто платит</label>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="payer_type" value="self"
                                           @if(old('payer_type', $user->payer_type) !== 'company') checked @endif>
                                    Лично
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="payer_type" value="company"
                                           @if(old('payer_type', $user->payer_type) === 'company') checked @endif>
                                    Компания
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($user->isClient())
                <!-- Почтовый адрес (только для клиентов) -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Получение сертификата/диплома</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="postal_address">Почтовый адрес для доставки (с индексом)</label>
                            <input type="text" class="form-control" id="postal_address"
                                   value="{{ old('postal_address', $user->postal_address) }}"
                                   name="postal_address" placeholder="Индекс, область, город, улица, дом, квартира">
                        </div>

                        <!-- Скан документа -->
                        <div class="form-group">
                            <label for="postal_doc">Скан документа</label>
                            @if($user->hasPostalDoc())
                                <div class="alert alert-info py-1">
                                    Загружен: {{ $user->postal_doc_name }}
                                    <a href="{{ $user->postalDocUrl() }}" target="_blank" class="btn btn-sm btn-info ml-2">Просмотр</a>
                                    <button type="button"
                                            data-url="{{ route('profile.postal-doc.delete') }}"
                                            data-token="{{ csrf_token() }}"
                                            data-user-id="{{ $user->id }}"
                                            class="btn btn-sm btn-danger ml-2 btn-delete-postal-doc">
                                        Удалить
                                    </button>
                                </div>
                            @endif
                            <input type="file" class="form-control-file" id="postal_doc" name="postal_doc" accept=".pdf">
                        </div>

                        <div class="form-group">
                            <label for="tracking_number">Трек-номер</label>
                            <input type="text" class="form-control" id="tracking_number"
                                   value="{{ old('tracking_number', $user->tracking_number) }}"
                                   name="tracking_number" placeholder="Номер для отслеживания почтового отправления">
                        </div>
                    </div>
                </div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-success">Сохранить</button>
                        <a href="{{ $user->isClient() ? route('users.clients') : route('users.employees') }}" class="btn btn-secondary">Отмена</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Блок документов (только для клиентов) -->
        @if($user->isClient())
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Документы пользователя: {{ $user->full_name }}</h3>
                    </div>
                    <div class="card-body">
                        <!-- Карточка компании -->
                        <div class="document-type-block mb-4 p-3 border rounded company-card-document-block" @if($user->payer_type !== 'company') style="display:none;" @endif>
                            <h5>Карточка компании</h5>

                            @if($user->hasCompanyCard())
                                <div class="document-item border p-2 mb-2 rounded border-success bg-light">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-sm-auto text-center text-sm-start mb-2 mb-sm-0">
                                            <span class="fas fa-file-pdf fa-2x text-danger"></span>
                                        </div>

                                        <div class="col-12 col-sm mb-2 mb-sm-0 text-center text-sm-start">
                                            <div class="fw-bold text-truncate" style="max-width: 200px;" title="{{ $user->company_card_name }}">
                                                {{ $user->company_card_name }}
                                            </div>
                                        </div>

                                        <div class="col-12 col-sm-auto text-center">
                                            <div class="btn-group btn-group-sm w-100 d-flex flex-wrap flex-sm-nowrap" role="group">
                                                <a href="{{ $user->companyCardUrl() }}"
                                                   class="btn btn-outline-primary d-flex align-items-center justify-content-center flex-fill flex-sm-grow-0" target="_blank">
                                                    <span class="d-none d-sm-inline">Скачать</span>
                                                    <span class="d-sm-none fas fa-download"></span>
                                                </a>
                                                <button type="button"
                                                        data-url="{{ route('users.company-card.delete', $user->id) }}"
                                                        data-token="{{ csrf_token() }}"
                                                        class="btn btn-outline-danger d-flex align-items-center justify-content-center btn-delete-company-card"
                                                        onclick="return confirm('Удалить карточку компании?')">
                                                    <span class="d-none d-sm-inline">Удалить</span>
                                                    <span class="d-sm-none fas fa-trash"></span>
                                                </button>
                                            </div>

                                            <div class="mt-1">
                                                <small class="text-success">
                                                    <span class="fas fa-check-circle"></span> Загружен
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <form action="{{ route('users.company-card.upload', $user->id) }}" method="POST"
                                  enctype="multipart/form-data" class="upload-form mt-2">
                                @csrf
                                <div class="row g-2">
                                    <div class="col-12 col-sm">
                                        <input type="file" name="company_card" class="form-control"
                                               accept=".pdf" {{ $user->hasCompanyCard() ? '' : 'required' }}>
                                    </div>
                                    <div class="col-12 col-sm-auto">
                                        <button type="submit" class="btn btn-primary w-100">Загрузить</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        @foreach($documentTypes as $docType)
                            <div class="document-type-block mb-4 p-3 border rounded">
                                <h5>{{ $docType['title'] }}</h5>

                                <form action="{{ route('documents.store') }}" method="POST"
                                      enctype="multipart/form-data" class="upload-form mt-2">
                                    @csrf
                                    <input type="hidden" name="type" value="{{ $docType['type'] }}">
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
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
                                        @include('partials.document-item-admin', ['document' => $doc])
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

    <script src="{{ asset('js/client-card.js') }}"></script>
    <script>
        function showImagePreview(imageUrl) {
            document.getElementById('previewImage').src = imageUrl;
            $('#imagePreviewModal').modal('show');
        }

        function closeImagePreview() {
            $('#imagePreviewModal').modal('hide');
        }

        $('#imagePreviewModal').on('click', function(e) {
            if (e.target === this) {
                closeImagePreview();
            }
        });

        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $('#imagePreviewModal').hasClass('show')) {
                closeImagePreview();
            }
        });
    </script>
@stop
