<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class DocumentsController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:50',
            'files' => 'required|array|min:1',
            'files.*' => 'required|file|mimes:pdf|max:51200',
            'user_id' => 'nullable|exists:users,id',
        ], [
            'type.required' => 'Пожалуйста, укажите тип документа',
            'type.max' => 'Тип документа должен быть не более 50 символов',
            'files.required' => 'Пожалуйста, выберите хотя бы один файл',
            'files.*.required' => 'Пожалуйста, выберите хотя бы один файл',
            'files.*.file' => 'Пожалуйста, выберите файл',
            'files.*.mimes' => 'Допустимые форматы файлов: pdf',
            'files.*.max' => 'Размер файла не должен превышать 50 МБ',
            'user_id.exists' => 'Неизвестный пользователь',
        ]);

        // Определяем пользователя для которого загружаем документы
        $targetUserId = $request->user_id ?? auth()->id();

        // Проверка прав: админ/менеджер может загружать для других, обычный пользователь - только для себя
        if ($targetUserId !== auth()->id() && ! auth()->user()->isAdmin() && ! auth()->user()->isManager()) {
            abort(403);
        }

        $files = $request->file('files');
        $uploadedCount = 0;

        foreach ($files as $file) {
            $mimeType = $file->getMimeType();

            if (str_starts_with($mimeType, 'image/')) {
                $image = Image::read($file);
                $encoded = $image->toJpeg(quality: 85);

                $path = 'documents/'.$targetUserId.'/'.uniqid().'.jpg';
                Storage::disk('local')->put($path, $encoded);

                $fileSize = strlen($encoded);
                $fileName = $file->getClientOriginalName();
                $finalMimeType = 'image/jpeg';
            } else {
                $path = $file->store('documents/'.$targetUserId, 'local');
                $fileSize = $file->getSize();
                $fileName = $file->getClientOriginalName();
                $finalMimeType = $mimeType;
            }

            Document::create([
                'user_id' => $targetUserId,
                'type' => $request->type,
                'file_path' => $path,
                'file_name' => $fileName,
                'mime_type' => $finalMimeType,
                'file_size' => $fileSize,
            ]);

            $uploadedCount++;
        }

        $message = $uploadedCount === 1
            ? 'Документ загружен'
            : "Загружено {$uploadedCount} документов";

        return back()
            ->with('success', $message);
    }

    public function destroy(Document $document)
    {
        // Владелец, админ или менеджер могут удалять
        if ($document->user_id !== auth()->id()
            && ! auth()->user()->isAdmin()
            && ! auth()->user()->isManager()) {
            abort(403);
        }

        // Клиент не может удалять одобренные документы
        if ($document->is_approved && ! auth()->user()->isAdmin() && ! auth()->user()->isManager()) {
            return back()
                ->with('error', 'Нельзя удалить одобренный документ');
        }

        $document->delete();

        return back()
            ->with('success', 'Документ удалён');
    }

    public function download(Document $document)
    {
        // Владелец, админ или менеджер могут скачивать
        if ($document->user_id !== auth()->id()
            && ! auth()->user()->isAdmin()
            && ! auth()->user()->isManager()) {
            abort(403);
        }

        return Storage::disk('local')->download($document->file_path, $document->file_name);
    }

    public function approve(Document $document)
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isManager()) {
            abort(403);
        }

        $document->update(['is_approved' => ! $document->is_approved]);

        $status = $document->is_approved ? 'одобрен' : 'одобрение отменено';

        return back()
            ->with('success', "Документ {$status}");
    }
}
