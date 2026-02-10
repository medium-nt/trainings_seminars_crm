<?php

namespace App\Services;

use App\Models\Group;
use App\Models\Payment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class PaymentService
{
    public function handleReceiptUpload(UploadedFile $file): array
    {
        $mimeType = $file->getMimeType();

        if (str_starts_with($mimeType, 'image/')) {
            $image = Image::read($file);
            $encoded = $image->toJpeg(quality: 85);

            $filename = uniqid().'.jpg';
            $receiptPath = 'receipts/'.$filename;
            Storage::disk('local')->put($receiptPath, $encoded);
        } else {
            $receiptPath = $file->store('receipts', 'local');
        }
        $receiptName = $file->getClientOriginalName();

        return [$receiptPath, $receiptName];
    }

    public function checkPaymentLimit(int $userId, int $groupId, ?int $excludePaymentId = null): bool
    {
        $query = Payment::where('user_id', $userId)
            ->where('group_id', $groupId);

        if ($excludePaymentId) {
            $query->where('id', '!=', $excludePaymentId);
        }

        return $query->count() < 3;
    }

    public function checkPaymentAmount(int $userId, int $groupId, float $amount, ?int $excludePaymentId = null): ?array
    {
        $group = Group::find($groupId);
        $groupPrice = $group?->price ?? 0;

        if ($groupPrice <= 0) {
            return null;
        }

        $query = Payment::where('user_id', $userId)
            ->where('group_id', $groupId);

        if ($excludePaymentId) {
            $query->where('id', '!=', $excludePaymentId);
        }

        $existingAmount = $query->sum('amount');
        $totalAmount = $existingAmount + $amount;
        $paymentsCount = $query->count();

        // Если будет 3 платежа - проверяем точное соответствие
        if ($paymentsCount >= 2) {
            if (round($totalAmount, 2) != round($groupPrice, 2)) {
                return [
                    'error' => sprintf('Сумма всех трёх платежей (%.2f ₽) должна быть равна стоимости группы (%.2f ₽). Разница: %.2f ₽.',
                        $totalAmount, $groupPrice, abs($totalAmount - $groupPrice)
                    ),
                ];
            }
        } elseif ($totalAmount > $groupPrice) {
            return [
                'error' => sprintf('Сумма всех платежей (%.2f ₽) превышает стоимость группы (%.2f ₽).',
                    $totalAmount, $groupPrice
                ),
            ];
        }

        return null;
    }
}
