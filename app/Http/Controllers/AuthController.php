<?php

namespace App\Http\Controllers;

use App\Helpers\SeoMeta;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function register()
    {
        $this->rememberSafeIntendedUrl(url()->previous());

        return Inertia::render('Register', [
            'siteKey' => config('services.cloudflare.site_key'),
            'meta' => SeoMeta::make(
                'Регистрация',
                null,
                null,
                ['robots' => 'noindex, nofollow'],
            ),
        ]);
    }

    public function login()
    {
        $this->rememberSafeIntendedUrl(url()->previous());

        return Inertia::render('Login', [
            'siteKey' => config('services.cloudflare.site_key'),
            'meta' => SeoMeta::make(
                'Авторизация',
                null,
                null,
                ['robots' => 'noindex, nofollow'],
            ),
        ]);
    }

    public function store(RegisterRequest $request)
    {
        $user = User::create([
            'username' => $request->tag,
            'nickname' => $request->nickname,
            'password' => $request->password,
        ]);

        Auth::login($user);

        return $this->authenticatedRedirect('Аккаунт успешно создан! Добро пожаловать');
    }

    public function authenticate(LoginRequest $request)
    {
        $credentials = [
            'username' => $request->tag,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            return $this->authenticatedRedirect('С возвращением!');
        }
        throw ValidationException::withMessages([
            'tag' => 'Неверное имя или пароль',
            'password' => 'Неверное имя или пароль',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Вы успешно вышли из аккаунта');
    }

    private function authenticatedRedirect(string $message)
    {
        $intended = session('url.intended');
        $path = $intended ? (parse_url($intended, PHP_URL_PATH) ?: '/') : null;

        // Filament (/admin) — не Inertia-страница: обычный redirect втянул бы её в SPA.
        if ($path !== null && str_starts_with($path, '/admin')) {
            session()->forget('url.intended');
            session()->flash('success', $message);

            return Inertia::location($intended);
        }

        return redirect()->intended(route('home'))->with('success', $message);
    }

    private function rememberSafeIntendedUrl(?string $url): void
    {
        if (! $url) {
            return;
        }

        $target = parse_url($url);
        $application = parse_url(url('/'));

        if (
            ! $target
            || ! isset($target['scheme'], $target['host'])
            || strcasecmp($target['scheme'], $application['scheme'] ?? '') !== 0
            || strcasecmp($target['host'], $application['host'] ?? '') !== 0
            || ($target['port'] ?? null) !== ($application['port'] ?? null)
            || str_starts_with($target['path'] ?? '/', '/auth/')
        ) {
            return;
        }

        $intended = $target['path'] ?? '/';
        if (isset($target['query'])) {
            $intended .= '?'.$target['query'];
        }

        session(['url.intended' => $intended]);
    }
}
