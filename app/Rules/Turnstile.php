<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;


class Turnstile implements ValidationRule{

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!config('services.cloudflare.enabled')) {
            return;
        }

        $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => config('services.cloudflare.secret'),
            'response' => $value,
            'remoteip' => request()->ip(),
        ]);

        if (!$response->json('success')) {
            $fail('Пожалуйста, подтвердите что вы не робот');
        }
    }
}
