<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Database\Seeder;

class IncidentSeeder extends Seeder
{
    public function run(): void
    {
        // البحث عن أول مستخدم عنده دور المواطن
        $citoyen = User::whereHas(
            'roles',
            fn ($q) => $q->where('name', 'citoyen')
        )->first();

        // أخذ أول تصنيف موجود في قاعدة البيانات
        $category = Category::first();

        // التحقق من وجود المواطن والتصنيف قبل إنشاء البلاغ
        if ($citoyen && $category) {
            Incident::create([
                'title' => 'Fuite d\'eau sur la voie publique',
                'description' => 'Une fuite d\'eau majeure constatée près du carrefour principal, risquant d\'endommager la chaussée.',
                'latitude' => 32.5353,
                'longitude' => -6.5342,

                // حالة البلاغ في البداية
                'status' => 'En attente',

                // أولوية البلاغ
                'priority' => 'Élevée',

                // ربط البلاغ بالمواطن
                'user_id' => $citoyen->id,

                // ربط البلاغ بالتصنيف
                'category_id' => $category->id,
            ]);
        }
    }
}