<?php

namespace Database\Seeders;

use App\Models\Affectation;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Database\Seeder;

class AffectationSeeder extends Seeder
{
    /**
     * تعبئة التكليفات التجريبية.
     */
    public function run(): void
    {
        // جلب أول مستخدم يحمل دور technicien
        $technicien = User::whereHas('roles', function ($query) {
            $query->where('name', 'technicien');
        })->first();

        // جلب أول بلاغين
        $incidents = Incident::limit(2)->get();

        // التحقق من وجود التقني والبلاغات
        if (!$technicien || $incidents->isEmpty()) {
            return;
        }

        // إنشاء تكليف لكل بلاغ
        foreach ($incidents as $incident) {
            Affectation::create([
                'incident_id' => $incident->id,
                'technicien_id' => $technicien->id,
                'date_affectation' => now(),
                'instructions' => 'Effectuer une vérification sur place et mettre à jour le statut après intervention.',
            ]);
        }
    }
}