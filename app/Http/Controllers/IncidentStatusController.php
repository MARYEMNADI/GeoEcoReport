<?php

namespace App\Http\Controllers;

use App\Models\Incident;
// استدعاء Trait الصلاحيات لتفادي خطأ undefined method authorize
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class IncidentStatusController extends Controller
{
    // تفعيل دالة $this->authorize() داخل الكنترولر
    use AuthorizesRequests;

    /**
     * تحديث حالة البلاغ البيئي (خاص بالتقني والأدمن)
     */
    public function updateStatus(
        Request $request,
        Incident $incident
    ): RedirectResponse {
        
        // 1. التحقق من الصلاحيات عبر IncidentPolicy
        $this->authorize('update', $incident);

        // 2. التحقق من صحة القيمة المدخلة للحالة
        $validated = $request->validate([
            'status' => [
                'required',
                'in:En attente,En cours de traitement,Résolu,Rejeté',
            ],
        ]);

        // 3. تحديث حالة البلاغ في قاعدة البيانات
        $incident->update([
            'status' => $validated['status'],
        ]);

        // 4. إعادة التوجيه لصفحة تفاصيل البلاغ مع رسالة نجاح
        return redirect()
            ->route('incidents.show', $incident)
            ->with(
                'success',
                'Le statut de l’incident a été mis à jour avec succès.'
            );
    }
}