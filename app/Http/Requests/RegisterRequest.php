<?php

namespace App\Http\Requests;

use App\Rules\Turnstile;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function rules(): array {
        return [
                'tag' => ['required', 'string', 'lowercase', 'regex:/^[a-z0-9_]{5,32}$/', 'unique:users,username'],
                'nickname' => ['required', 'string', 'min:1', 'max:64'],
                'password' => ['required', 'string', 'min:8', 'max:72', 'confirmed'],
                'password_confirmation' => ['required','same:password'],
                'cf-turnstile-response' => config('services.cloudflare.enabled') ? ['required', new Turnstile] : ['nullable'],
            ];
    }

    public function messages(): array {
        return [
            'tag.unique' => 'Это имя пользователя уже занято',
            'tag.regex' => "От 5 до 32 символов: только маленькая латиница, цифры и '_'",
            'tag.lowercase' => "От 5 до 32 символов: только маленькая латиница, цифры и '_'",
            'nickname.min'=>'Не менее 1 и не более 64 символов',
            'nickname.max'=>'Не менее 1 и не более 64 символов',
            'password.min'=>'Не менее 8 и не более 72 символов',
            'password.max'=>'Не менее 8 и не более 72 символов',
            'password.confirmed' => 'Пароли не совпадают',
            'password_confirmation.same' => 'Пароли не совпадают',
            'password_confirmation.required' => 'Поле обязательно',
            'tag.required' => 'Поле обязательно',
            'nickname.required' => 'Поле обязательно',
            'password.required' => 'Поле обязательно',
            'cf-turnstile-response.required'=>'Пожалуйста, подтвердите что вы не робот',
        ];
    }

    public function authorize(): bool {
        return true;
    }
}
