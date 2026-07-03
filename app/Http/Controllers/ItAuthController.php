<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ItAuthController extends Controller
{
    public function create(): View
    {
        return view('auth.it-login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $user = User::query()
            ->where('email', $credentials['email'])
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง',
            ]);
        }

        if (! in_array($user->role, [User::ROLE_ADMIN, User::ROLE_IT_DEV, User::ROLE_IT_TEST], true)) {
            throw ValidationException::withMessages([
                'email' => 'บัญชีนี้ไม่ใช่บัญชีทีม IT',
            ]);
        }

        if (! $user->isActive()) {
            throw ValidationException::withMessages([
                'email' => 'บัญชีนี้ยังไม่พร้อมใช้งาน',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()
            ->intended(route('requests.queue'))
            ->with('success', 'เข้าสู่ระบบเรียบร้อยแล้ว');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('home')
            ->with('success', 'ออกจากระบบเรียบร้อยแล้ว');
    }
}
