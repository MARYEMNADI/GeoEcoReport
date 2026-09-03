<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. إنشاء حساب المدير (Admin)
        $admin = User::firstOrCreate(
            ['email' => 'admin@geoeco.ma'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('password123'),
            ]
        );
        $admin->addRole('administrateur');

        // 2. إنشاء حساب التقني (Technician)
        $technician = User::firstOrCreate(
            ['email' => 'tech@geoeco.ma'],
            [
                'name' => 'Technicien Test',
                'password' => Hash::make('password123'),
            ]
        );
        $technician->addRole('technicien');

        // 3. إنشاء حساب المواطن (Citizen)
        $citoyen = User::firstOrCreate(
            ['email' => 'citoyen@geoeco.ma'],
            [
                'name' => 'Citoyen Test',
                'password' => Hash::make('password123'),
            ]
        );
        $citoyen->addRole('citoyen');
    }
}