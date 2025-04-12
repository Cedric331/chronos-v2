<?php

namespace Database\Seeders;

use App\Models\TicketCategory;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\TicketTag;
use Illuminate\Database\Seeder;

class TicketSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Création des statuts par défaut
        $statuses = [
            [
                'name' => 'Nouveau',
                'color' => '#3498db',
                'icon' => 'fas fa-plus-circle',
                'order' => 1,
                'is_default' => true,
                'is_closed' => false,
            ],
            [
                'name' => 'En cours',
                'color' => '#f39c12',
                'icon' => 'fas fa-spinner',
                'order' => 2,
                'is_default' => false,
                'is_closed' => false,
            ],
            [
                'name' => 'En attente',
                'color' => '#9b59b6',
                'icon' => 'fas fa-clock',
                'order' => 3,
                'is_default' => false,
                'is_closed' => false,
            ],
            [
                'name' => 'Résolu',
                'color' => '#2ecc71',
                'icon' => 'fas fa-check-circle',
                'order' => 4,
                'is_default' => false,
                'is_closed' => true,
            ],
            [
                'name' => 'Fermé',
                'color' => '#7f8c8d',
                'icon' => 'fas fa-times-circle',
                'order' => 5,
                'is_default' => false,
                'is_closed' => true,
            ],
            [
                'name' => 'Rejeté',
                'color' => '#e74c3c',
                'icon' => 'fas fa-ban',
                'order' => 6,
                'is_default' => false,
                'is_closed' => true,
            ],
        ];

        foreach ($statuses as $status) {
            TicketStatus::create($status);
        }

        // Création des catégories par défaut
        $categories = [
            [
                'name' => 'Bug',
                'color' => '#e74c3c',
                'icon' => 'fas fa-bug',
                'description' => 'Problème technique ou erreur dans l\'application',
                'is_default' => true,
            ],
            [
                'name' => 'Amélioration',
                'color' => '#3498db',
                'icon' => 'fas fa-lightbulb',
                'description' => 'Suggestion d\'amélioration pour une fonctionnalité existante',
                'is_default' => false,
            ],
            [
                'name' => 'Nouvelle fonctionnalité',
                'color' => '#2ecc71',
                'icon' => 'fas fa-plus',
                'description' => 'Demande d\'une nouvelle fonctionnalité',
                'is_default' => false,
            ],
            [
                'name' => 'Question',
                'color' => '#f39c12',
                'icon' => 'fas fa-question-circle',
                'description' => 'Question sur l\'utilisation de l\'application',
                'is_default' => false,
            ],
            [
                'name' => 'Autre',
                'color' => '#9b59b6',
                'icon' => 'fas fa-ellipsis-h',
                'description' => 'Autre type de demande',
                'is_default' => false,
            ],
        ];

        foreach ($categories as $category) {
            TicketCategory::create($category);
        }

        // Création des priorités par défaut
        $priorities = [
            [
                'name' => 'Basse',
                'color' => '#3498db',
                'icon' => 'fas fa-arrow-down',
                'level' => 1,
                'is_default' => true,
            ],
            [
                'name' => 'Moyenne',
                'color' => '#f39c12',
                'icon' => 'fas fa-minus',
                'level' => 2,
                'is_default' => false,
            ],
            [
                'name' => 'Haute',
                'color' => '#e67e22',
                'icon' => 'fas fa-arrow-up',
                'level' => 3,
                'is_default' => false,
            ],
            [
                'name' => 'Critique',
                'color' => '#e74c3c',
                'icon' => 'fas fa-exclamation-triangle',
                'level' => 4,
                'is_default' => false,
            ],
        ];

        foreach ($priorities as $priority) {
            TicketPriority::create($priority);
        }

        // Création des tags par défaut
//        $tags = [
//            ['name' => 'Interface', 'color' => '#3498db'],
//            ['name' => 'Performance', 'color' => '#f39c12'],
//            ['name' => 'Sécurité', 'color' => '#e74c3c'],
//            ['name' => 'Documentation', 'color' => '#2ecc71'],
//            ['name' => 'Mobile', 'color' => '#9b59b6'],
//            ['name' => 'API', 'color' => '#34495e'],
//        ];
//
//        foreach ($tags as $tag) {
//            TicketTag::create($tag);
//        }
    }
}
