<?php
 
namespace App\Http\Requests;

use App\Rules\Turnstile;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{

    public function rules(): array {
        return [
            'tag' => ['required', 'string'],
            'password' => ['required', 'string'],
            'cf-turnstile-response' => ['required', new Turnstile],
        ];
    }

    public function messages(): array {
        return [
            'cf-turnstile-response.required'=>'Пожалуйста, подтвердите что вы не робот',
        ];
    }

    public function authorize(): bool {
        return true;
    }
}
