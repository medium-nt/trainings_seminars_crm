<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UserService
{
    public function setPasswordIfNeeded(array $data, ?string $password): array
    {
        if ($password) {
            $data['password'] = bcrypt($password);
        } else {
            unset($data['password']);
        }

        return $data;
    }

    public function handleCompanyCardUpload(array $data, User $user): array
    {
        if (isset($data['company_card']) && $data['company_card'] instanceof \Illuminate\Http\UploadedFile) {
            // Удаляем старый файл, если есть
            if ($user->company_card_path) {
                Storage::delete($user->company_card_path);
            }

            // Сохраняем новый файл
            $file = $data['company_card'];
            $path = $file->store("documents/{$user->id}/company_card", 'public');

            $data['company_card_path'] = $path;
            $data['company_card_name'] = $file->getClientOriginalName();
        }

        // Убираем company_card из data после обработки
        unset($data['company_card']);

        return $data;
    }

    public function handlePayerTypeChange(array $data, User $user): array
    {
        // Если payer_type не передан — сохраняем текущее значение
        if (! isset($data['payer_type'])) {
            $data['payer_type'] = $user->payer_type;
        }

        // Если тип изменился с company на self — удаляем файл
        if (isset($data['payer_type']) && $data['payer_type'] === 'self' && $user->payer_type === 'company') {
            if ($user->company_card_path) {
                Storage::delete($user->company_card_path);
            }
            $data['company_card_path'] = null;
            $data['company_card_name'] = null;
        }

        return $data;
    }
}
