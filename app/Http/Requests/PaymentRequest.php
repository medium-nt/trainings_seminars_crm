<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $isUpdate = $this->route('payment') !== null;

        $rules = [
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01|max:999999.99',
            'group_id' => 'required|exists:groups,id',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:51200',
        ];

        // user_id обязательный только при создании
        if (! $isUpdate) {
            $rules['user_id'] = 'required|exists:users,id';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'payment_date.required' => 'Укажите дату платежа',
            'payment_date.date' => 'Некорректная дата',
            'amount.required' => 'Укажите сумму платежа',
            'amount.numeric' => 'Сумма должна быть числом',
            'amount.min' => 'Минимальная сумма: 0.01',
            'user_id.required' => 'Выберите студента',
            'group_id.required' => 'Выберите группу',
            'receipt.mimes' => 'Допустимые форматы: JPG, JPEG, PNG, PDF',
            'receipt.max' => 'Максимальный размер файла: 50 МБ',
        ];
    }
}
