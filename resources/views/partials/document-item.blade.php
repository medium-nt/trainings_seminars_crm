<div class="document-item border p-2 mb-2 rounded @if($document->is_approved) border-success @else border-warning @endif bg-light">
    <div class="row align-items-center">
        <div class="col-12 col-sm-auto text-center text-sm-start mb-2 mb-sm-0">
            @if($document->isImage())
                <a href="#" onclick="showImagePreview('{{ route('documents.download', $document) }}'); return false;"
                   class="d-inline-block" style="cursor: pointer;">
                    <img src="{{ route('documents.download', $document) }}" alt="{{ $document->file_name }}"
                         class="img-thumbnail" style="max-width: 80px; max-height: 80px;"
                         onerror="this.src='https://via.placeholder.com/80?text=Ошибка'">
                </a>
            @else
                <span class="fas fa-file-pdf fa-2x text-danger"></span>
            @endif
        </div>

        <div class="col-12 col-sm mb-2 mb-sm-0 text-center text-sm-start">
            <div class="fw-bold text-truncate" style="max-width: 200px;" title="{{ $document->file_name }}">
                {{ $document->file_name }}
            </div>
            <small class="text-muted">{{ number_format($document->file_size / 1024 / 1024, 2) }} MB</small>
        </div>

        <div class="col-12 col-sm-auto text-center">
            <div class="btn-group btn-group-sm w-100 d-flex" role="group">
                <a href="{{ route('documents.download', $document) }}"
                   class="btn btn-outline-primary d-flex align-items-center justify-content-center flex-fill flex-sm-grow-0">
                    <span class="d-none d-sm-inline">Скачать</span>
                    <span class="d-sm-none fas fa-download"></span>
                </a>
                <form action="{{ route('documents.destroy', $document) }}" method="POST" class="d-inline">
                    @method('DELETE')
                    @csrf
                    <button type="submit" class="btn btn-outline-danger d-flex align-items-center justify-content-center"
                            onclick="return confirm('Удалить документ?')">
                        <span class="d-none d-sm-inline">Удалить</span>
                        <span class="d-sm-none fas fa-trash"></span>
                    </button>
                </form>
            </div>

            <!-- Статус -->
            <div class="mt-1">
                <small @if($document->is_approved) class="text-success" @else class="text-warning" @endif>
                    @if($document->is_approved)
                        <span class="fas fa-check-circle"></span> Одобрен
                    @else
                        <span class="fas fa-clock"></span> Ожидает проверки
                    @endif
                </small>
            </div>
        </div>
    </div>
</div>
