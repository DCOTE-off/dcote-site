<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(){
        return view('pages.reg');
    }

    public function login(){
        return view('pages.login');
    }
}
