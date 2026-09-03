<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Nids-de-poule',
                'type' => 'Urbain',
                'description' => 'Dégradation de la chaussée ou présence de trous sur la route.',
            ],
            [
                'name' => 'Éclairage public défectueux',
                'type' => 'Urbain',
                'description' => 'Panne ou dysfonctionnement de l’éclairage public.',
            ],
            [
                'name' => 'Signalisation endommagée',
                'type' => 'Urbain',
                'description' => 'Panne ou détérioration de la signalisation routière.',
            ],
            [
                'name' => 'Fuite d’eau',
                'type' => 'Urbain',
                'description' => 'Fuite ou problème au niveau du réseau d’eau.',
            ],
            [
                'name' => 'Déchets ménagers',
                'type' => 'Urbain',
                'description' => 'Accumulation ou problème lié aux déchets ménagers.',
            ],
            [
                'name' => 'Pollution de l’air',
                'type' => 'Environnemental',
                'description' => 'Présence de fumée, gaz ou autres polluants atmosphériques.',
            ],
            [
                'name' => 'Pollution de l’eau',
                'type' => 'Environnemental',
                'description' => 'Contamination ou dégradation de la qualité de l’eau.',
            ],
            [
                'name' => 'Dépôt sauvage de déchets',
                'type' => 'Environnemental',
                'description' => 'Dépôt illégal de déchets dans un espace public ou naturel.',
            ],
            [
                'name' => 'Dégradation des espaces verts',
                'type' => 'Environnemental',
                'description' => 'Dégradation ou détérioration d’un espace vert.',
            ],
            [
                'name' => 'Coupe illégale d’arbres',
                'type' => 'Environnemental',
                'description' => 'Abattage ou coupe non autorisée d’arbres.',
            ],
            [
                'name' => 'Pollution sonore',
                'type' => 'Environnemental',
                'description' => 'Nuisances sonores importantes dans une zone donnée.',
            ],
            [
                'name' => 'Incendie',
                'type' => 'Environnemental',
                'description' => 'Incendie affectant une zone urbaine, naturelle ou forestière.',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}