<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    public function register() {
        $this->rememberSafeIntendedUrl(url()->previous());
        return view('pages.reg');
    }

    public function login() {
        $this->rememberSafeIntendedUrl(url()->previous());

        return view('pages.login');
    }

    public function store(RegisterRequest $request) {
            $user = User::create([
                'username' => $request->tag,
                'nickname' => $request->nickname,
                'password' => $request->password,
            ]);
            
            Auth::login($user);
            
            return redirect()->intended(route('home'))->with('success', 'Аккаунт успешно создан! Добро пожаловать');
    }

    public function authenticate(LoginRequest $request)
    {
        $credentials = [
            'username' => $request->tag,
            'password' => $request->password,
        ];
        
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('home'))->with('success', 'С возвращением!');
        }
        return back()->with('error', 'Неверное имя или пароль.')->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')->with('success', 'Вы успешно вышли из аккаунта.');
    }

    private function rememberSafeIntendedUrl(?string $url): void
    {
        if (!$url) {
            return;
        }

        $target = parse_url($url);
        $application = parse_url(url('/'));

        if (
            !$target
            || !isset($target['scheme'], $target['host'])
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
