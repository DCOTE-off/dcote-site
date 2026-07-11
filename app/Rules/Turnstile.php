<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;


class Turnstile implements ValidationRule{

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!config('services.cloudflare.enabled')) {
            return;
        }

        try {
            $response = Http::asForm()
                ->connectTimeout(2)
                ->timeout(5)
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret' => config('services.cloudflare.secret'),
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]);
        } catch (ConnectionException) {
            $fail('Сервис проверки временно недоступен. Пожалуйста, попробуйте ещё раз.');
            return;
        }

        if (!$response->successful() || !$response->json('success')) {
            $fail('Пожалуйста, подтвердите что вы не робот');
        }
    }
}
