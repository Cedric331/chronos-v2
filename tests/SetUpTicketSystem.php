<?php

namespace Tests;

use App\Models\TicketCategory;
use App\Models\TicketPriority;
use App\Models\TicketStatus;

trait SetUpTicketSystem
{
    /**
     * Crée les données nécessaires pour le système de tickets
     */
    protected function setUpTicketSystem(): void
    {
        // Créer les statuts par défaut
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
                'name' => 'Résolu',
                'color' => '#2ecc71',
                'icon' => 'fas fa-check-circle',
                'order' => 4,
                'is_default' => false,
                'is_closed' => true,
            ],
        ];

        foreach ($statuses as $status) {
            TicketStatus::firstOrCreate(
                ['name' => $status['name']],
                $status
            );
        }

        // Créer les catégories par défaut
        $categories = [
            [
                'name' => 'Bug',
                'color' => '#e74c3c',
                'icon' => 'fas fa-bug',
                'description' => 'Problème technique',
                'is_default' => true,
            ],
        ];

        foreach ($categories as $category) {
            TicketCategory::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }

        // Créer les priorités par défaut
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
        ];

        foreach ($priorities as $priority) {
            TicketPriority::firstOrCreate(
                ['name' => $priority['name']],
                $priority
            );
        }
    }
}

