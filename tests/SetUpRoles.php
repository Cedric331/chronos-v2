<?php

namespace Tests;

use Spatie\Permission\Models\Role;

trait SetUpRoles
{
    /**
     * Crée les rôles nécessaires pour les tests
     */
    protected function setUpRoles(): void
    {
        $roles = ['Administrateur', 'Responsable', 'Coordinateur', 'Conseiller'];

        foreach ($roles as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);
        }
    }
}

