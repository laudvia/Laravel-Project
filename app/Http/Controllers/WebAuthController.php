<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class WebAuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required','string'],
        ]);

        if (!Auth::attempt($credentials, true)) {
            return back()
                ->withErrors(['email' => 'Неверный email или пароль'])
                ->withInput();
        }

        $request->session()->regenerate();
        return redirect('/articles')->with('success', 'Вы вошли в систему');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'                  => ['required','string','max:255'],
            'email'                 => ['required','email','max:255','unique:users,email'],
            'password'              => ['required','string','min:8','confirmed'],
        ]);

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);

        // Если есть роли — назначим reader, но безопасно (не ломаем проект)
        try {
            if (class_exists(\App\Models\Role::class) && property_exists($user, 'role_id')) {
                $roleId = \App\Models\Role::where('slug', 'reader')->value('id')
                    ?: \App\Models\Role::where('name', 'reader')->value('id');
                if ($roleId) $user->role_id = $roleId;
            }
        } catch (\Throwable $e) {}

        $user->save();

        Auth::login($user, true);
        $request->session()->regenerate();
        return redirect('/articles')->with('success', 'Регистрация успешна');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/articles')->with('success', 'Вы вышли из системы');
    }
}
