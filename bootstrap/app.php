<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    // 1. تحديد ملفات المسارات (Routing Configuration)
    ->withRouting(
        web: __DIR__.'/../routes/web.php',       // مسارات واجهات الويب
        commands: __DIR__.'/../routes/console.php', // أوامر Artisan المخصصة
        health: '/up',                           // مسار فحص حالة التطبيق (Health Check)
    )
    
    // 2. تسجيل وإدارة الـ Middleware
    ->withMiddleware(function (Middleware $middleware): void {
        // تسجيل اختصار (Alias) لـ RoleMiddleware لاستخدامه بسهولة داخل المسارات
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    
    // 3. معالجة الاستثناءات والأخطاء (Exception Handling)
    ->withExceptions(function (Exceptions $exceptions): void {
        // يمكن تخصيص صفحات الخطأ أو طريقة التعامل مع Exceptions هنا
    })
    
    // 4. إنشـاء وبنـاء التطبيـق
    ->create();