<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class LaratrustSeeder extends Seeder
{
    public function run(): void
    {
        // 1. إنشاء الأدوار الثلاثة في النظام (أو جلبها إذا كانت موجودة من قبل)
        $citoyen = Role::firstOrCreate(
            ['name' => 'citoyen'], // البحث بهذا الاسم
            [
                'display_name' => 'Citoyen',
                'description' => 'Utilisateur pouvant créer et suivre des signalements.', // دور المواطن: إنشاء ومتابعة البلاغات
            ]
        );

        $technicien = Role::firstOrCreate(
            ['name' => 'technicien'],
            [
                'display_name' => 'Technicien',
                'description' => 'Utilisateur chargé de traiter les incidents affectés.', // دور التقني: معالجة البلاغات المحالة إليه
            ]
        );

        $admin = Role::firstOrCreate(
            ['name' => 'administrateur'],
            [
                'display_name' => 'Administrateur',
                'description' => 'Utilisateur responsable de la gestion globale.', // دور المدير: الإدارة الشاملة
            ]
        );

        // 2. قائمة الصلاحيات المطلوبة في التطبيق
        $permissions = [
            ['name' => 'create-incidents', 'display_name' => 'Créer des incidents'], // إنشاء بلاغ
            ['name' => 'view-incidents', 'display_name' => 'Consulter les incidents'], // عرض البلاغات
            ['name' => 'update-incidents', 'display_name' => 'Modifier les incidents'], // تعديل البلاغ
            ['name' => 'delete-incidents', 'display_name' => 'Supprimer les incidents'], // حذف البلاغ
            ['name' => 'manage-categories', 'display_name' => 'Gérer les catégories'], // إدارة التصنيفات
            ['name' => 'manage-users', 'display_name' => 'Gérer les utilisateurs'], // إدارة المستخدمين
            ['name' => 'assign-incidents', 'display_name' => 'Affecter les incidents'], // تعيين بلاغ لتقني
            ['name' => 'update-incident-status', 'display_name' => 'Modifier le statut'], // تغيير حالة البلاغ
            ['name' => 'add-comments', 'display_name' => 'Ajouter des commentaires'], // إضافة تعليق
            ['name' => 'view-statistics', 'display_name' => 'Consulter les statistiques'], // عرض الإحصائيات
            ['name' => 'use-ai-assistant', 'display_name' => 'Utiliser GeoEco Assistant'], // استخدام الذكاء الاصطناعي
        ];

        $permissionModels = [];

        // إنشاء الصلاحيات وحفظها في مصفوفة للاستخدام في الخطوات التالية
        foreach ($permissions as $permission) {
            $permissionModels[$permission['name']] = Permission::firstOrCreate(
                ['name' => $permission['name']],
                ['display_name' => $permission['display_name']]
            );
        }

        // 3. تعيين صلاحيات المواطن (إنشاء، عرض، تعليق، واستخدام المساعد الذكي)
        $citoyen->givePermissions([
            $permissionModels['create-incidents'],
            $permissionModels['view-incidents'],
            $permissionModels['add-comments'],
            $permissionModels['use-ai-assistant'],
        ]);

        // 4. تعيين صلاحيات التقني (عرض البلاغات، تغيير الحالة، وإضافة تعليقات)
        $technicien->givePermissions([
            $permissionModels['view-incidents'],
            $permissionModels['update-incident-status'],
            $permissionModels['add-comments'],
        ]);

        // 5. منح جميع الصلاحيات للمدير
        $admin->givePermissions(Permission::all());
    }
}