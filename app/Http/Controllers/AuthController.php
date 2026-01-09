<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

final class AuthController extends Controller
{
    /**
     * GET /register
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * POST /register
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // После успешной регистрации — отправляем пользователя на форму авторизации.
        return redirect()->route('login')->with('status', 'Пользователь зарегистрирован. Выполните вход.');
    }

    /**
     * GET /login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * POST /login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Неверные учетные данные.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        // Sanctum: создаём персональный токен и сохраняем его идентификатор в сессии,
        // чтобы при выходе удалить.
        $user = $request->user();
        $token = $user->createToken('web');

        $request->session()->put('sanctum_token_id', $token->accessToken->id);
        $request->session()->put('sanctum_plain_text_token', $token->plainTextToken);

        // После авторизации — редирект на главную страницу (в обход middleware auth).
        return redirect('/');
    }

    /**
     * POST /logout
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        // Удаляем токен аутентификации (если создавали при входе).
        $tokenId = $request->session()->get('sanctum_token_id');
        if ($user && $tokenId) {
            $user->tokens()->where('id', $tokenId)->delete();
        }

        Auth::logout();

        // Аннулируем сессию и обновляем CSRF.
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
