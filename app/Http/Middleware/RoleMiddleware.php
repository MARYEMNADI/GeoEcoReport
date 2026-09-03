<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * معالجة الطلب والتأكد من تسجيل المستخدم وامتلاكه للدور المطلوبة.
     */
    public function handle(
        Request $request,
        Closure $next,
        string $role // قبول اسم الدور كـ parameter (مثلاً: citoyen, technicien, administrateur)
    ): Response {
        // 1. التحقق مما إذا كان المستخدم مسجلاً للدخول، وفي حال العكس التوجيه لصفحة الـ Login
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // 2. استخدام دالة hasRole الخاصة بـ Laratrust للتأكد من امتلاك المستخدم للدور الممرر
        if (!$request->user()->hasRole($role)) {
            // في حال عدم امتلاك الدور يتم إيقاف الطلب مع إرجاع خطأ 403 (Non Autorisé)
            abort(403, 'Accès non autorisé.');
        }

        // 3. السماح بتمرير الطلب للمرحلة التالية (Controller)
        return $next($request);
    }
}