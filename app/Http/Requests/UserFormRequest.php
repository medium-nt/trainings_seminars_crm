<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserFormRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isUpdate = $this->route('user') !== null;
        $isProfile = $this->route()->getName() === 'profile.update';

        // Определяем доступные роли для создания
        $availableRoles = auth()->user()->isAdmin() ? '1,2,4' : '1';

        $rules = [
            'last_name' => 'required|string|min:2|max:255',
            'name' => 'required|string|min:2|max:255',
            'patronymic' => 'nullable|string|min:2|max:255',
            'email' => 'required|email|max:255|unique:users',
            'phone' => 'nullable|string|min:8|max:15',
            'role_id' => 'required|in:'.$availableRoles,
            'payer_type' => 'nullable|in:self,company',
            'company_card' => 'nullable|file|mimes:pdf|max:51200',
            'postal_address' => 'nullable|string|max:500',
            'postal_doc' => 'nullable|file|mimes:pdf|max:51200',
            'tracking_number' => 'nullable|string|max:50',
        ];

        if ($isProfile) {
            $user = auth()->user();
            $rules['email'] = 'required|email|max:255|unique:users,email,'.$user->id;
            $rules['password'] = 'nullable|confirmed|string|min:6';

            // Файл обязателен только если выбрана "Компания" и нет загруженного файла
            if (! $user->hasCompanyCard()) {
                $rules['company_card'] = 'required_if:payer_type,company|file|mimes:pdf|max:51200';
            }
            unset($rules['role_id']);
        } elseif ($isUpdate) {
            $user = $this->route('user');
            $rules['email'] = 'required|email|max:255|unique:users,email,'.$user->id;
            $rules['password'] = 'nullable|confirmed|string|min:6';

            // Файл обязателен только если выбрана "Компания" и нет загруженного файла
            if (! $user->hasCompanyCard()) {
                $rules['company_card'] = 'required_if:payer_type,company|file|mimes:pdf|max:51200';
            }
            unset($rules['role_id']);
        } else {
            $rules['password'] = 'required|confirmed|string|min:6';
            // При создании нового пользователя payer_type обязателен
            $rules['payer_type'] = 'required|in:self,company';
            // При создании нового пользователя файл обязателен если выбрана "Компания"
            $rules['company_card'] = 'required_if:payer_type,company|file|mimes:pdf|max:51200';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'last_name.required' => 'Пожалуйста, введите фамилию',
            'last_name.min' => 'Фамилия должна быть не менее 2 символов',
            'last_name.max' => 'Фамилия должна быть не более 255 символов',
            'name.required' => 'Пожалуйста, введите имя',
            'name.min' => 'Имя должно быть не менее 2 символов',
            'name.max' => 'Имя должно быть не более 255 символов',
            'patronymic.min' => 'Отчество должно быть не менее 2 символов',
            'patronymic.max' => 'Отчество должно быть не более 255 символов',
            'email.required' => 'Пожалуйста, введите адрес электронной почты',
            'email.email' => 'Пожалуйста, введите корректный адрес электронной почты',
            'email.max' => 'Адрес электронной почты должен быть не более 255 символов',
            'email.unique' => 'Пользователь с таким адресом электронной почты уже существует',
            'phone.min' => 'Номер телефона должен быть не менее 8 символов',
            'phone.max' => 'Номер телефона должен быть не более 15 символов',
            'password.required' => 'Пожалуйста, введите пароль',
            'password.min' => 'Пароль должен быть не менее 6 символов',
            'password.confirmed' => 'Пароли не совпадают',
            'role_id.required' => 'Пожалуйста, выберите роль',
            'role_id.in' => 'Выбранная роль не существует',
            'payer_type.required' => 'Пожалуйста, выберите, кто платит',
            'payer_type.in' => 'Некорректное значение плательщика',
            'company_card.required_if' => 'Пожалуйста, загрузите карточку компании',
            'company_card.mimes' => 'Карточка компании должна быть в формате PDF',
            'company_card.max' => 'Размер файла не должен превышать 50 МБ',
            'postal_address.max' => 'Почтовый адрес должен быть не более 500 символов',
            'postal_doc.mimes' => 'Скан документа должен быть в формате PDF',
            'postal_doc.max' => 'Размер скана документа не должен превышать 50 МБ',
            'tracking_number.max' => 'Трек-номер должен быть не более 50 символов',
        ];
    }
}
