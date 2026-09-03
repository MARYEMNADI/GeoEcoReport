<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * يتم تشغيل جميع ملفات Seeder بالترتيب المناسب
     * لتجنب مشاكل العلاقات والمفاتيح الأجنبية.
     */
    public function run(): void
    {
        $this->call([
            LaratrustSeeder::class, // 1. إنشاء Roles و Permissions
            UserSeeder::class,      // 2. إنشاء المستخدمين وربطهم بالأدوار
            CategorySeeder::class,  // 3. إنشاء تصنيفات الحوادث
            IncidentSeeder::class,  // 4. إنشاء الحوادث وربطها بالمستخدمين والتصنيفات
        ]);
    }
}
