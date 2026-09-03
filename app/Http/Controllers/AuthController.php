<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * عرض صفحة تسجيل الدخول (Login Form).
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * معالجة طلب تسجيل الدخول والمصادقة على بيانات المستخدم.
     */
    public function login(Request $request)
    {
        // 1. التحقق من صحة المدخلات (Validation)
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. محاولة تسجيل الدخول وإعادة إنشاء الجلسة لمنع هجمات Session Fixation
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('/incidents');
        }

        // 3. الإرجاع في حال فشل المصادقة مع رسالة خطأ
        return back()->withErrors([
            'email' => 'بيانات الدخول غير صحيحة.',
        ])->onlyInput('email');
    }

    /**
     * عرض صفحة إنشاء حساب جديد (Registration Form).
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * معالجة إنشاء حساب جديد، وتشفير كلمة المرور، وتعيين دور "مواطن" (citoyen) تلقائياً.
     */
    public function register(Request $request)
    {
        // 1. التحقق من قيوّد البيانات الممررة
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // 2. إنشاء المستخدم وحفظه في قاعدة البيانات
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 3. ربط المستخدم الجديد بدور "citoyen" عبر Laratrust افتراضياً
        $citoyenRole = Role::where('name', 'citoyen')->first();
        if ($citoyenRole) {
            $user->addRole($citoyenRole);
        }

        // 4. تسجيل دخول المستخدم فور إنشاء الحساب وإعادة توجيهه
        Auth::login($user);

        return redirect()->route('incidents.index');
    }

    /**
     * تسجيل خروج المستخدم وتدمير الجلسة الحالية وإعادة تعيين الـ Token.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}