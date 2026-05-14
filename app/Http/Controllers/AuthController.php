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
        return view('pages.reg');
    }

    public function login() {
        return view('pages.login');
    }

    public function store(RegisterRequest $request) {
        $user = User::create([
            'username' => $request->tag,
            'nickname' => $request->nickname,
            'password' => $request->password,
        ]);
        Auth::login($user);
        return redirect()->route('home')->with('success', 'Аккаунт успешно создан! Добро пожаловать');
    }



    public function authenticate(LoginRequest $request)
    {
        $credentials = [
            'username' => $request->tag,
            'password' => $request->password,
        ];
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->route('home')->with('success', 'С возвращением!');
        }

        return back()->with('error', 'Неверное имя или пароль.')->withInput();
    }

}
