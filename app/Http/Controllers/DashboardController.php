<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /**
     * عرض لوحة التحكم الخاصة بـ "المواطن" (Citoyen).
     * تُرجع واجهة Blade الموجودة في: resources/views/dashboard/citoyen.blade.php
     */
    public function citoyen(): View
    {
        return view('dashboard.citoyen');
    }

    /**
     * عرض لوحة التحكم الخاصة بـ "التقني" (Technicien).
     * تُرجع واجهة Blade الموجودة في: resources/views/dashboard/technicien.blade.php
     */
    public function technicien(): View
    {
        return view('dashboard.technicien');
    }

    /**
     * عرض لوحة التحكم الخاصة بـ "المدير" (Administrateur).
     * تُرجع واجهة Blade الموجودة في: resources/views/dashboard/admin.blade.php
     */
    public function admin(): View
    {
        return view('dashboard.admin');
    }
}
