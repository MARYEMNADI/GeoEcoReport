<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        // جلب أول بلاغ وأول مستخدمين في النظام
        $incident = Incident::first();
        $admin = User::whereHas('roles', fn ($q) => $q->where('name', 'administrateur'))->first();
        $technician = User::whereHas('roles', fn ($q) => $q->where('name', 'technicien'))->first();

        if ($incident && $admin && $technician) {
            Comment::create([
                'incident_id' => $incident->id,
                'user_id' => $admin->id,
                'content' => 'Signalement bien reçu. Équipe technique notifiée.',
            ]);

            Comment::create([
                'incident_id' => $incident->id,
                'user_id' => $technician->id,
                'content' => 'Intervention planifiée pour traiter cet incident.',
            ]);
        }
    }
}