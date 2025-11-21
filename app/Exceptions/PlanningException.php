<?php

namespace App\Exceptions;

use Exception;

class PlanningException extends Exception
{
    public static function invalidDateRange(): self
    {
        return new self('La date de début doit être antérieure à la date de fin.', 422);
    }

    public static function userNotFound(int $userId): self
    {
        return new self("L'utilisateur avec l'ID {$userId} n'existe pas.", 404);
    }

    public static function insufficientPermissions(): self
    {
        return new self('Vous n\'avez pas les permissions nécessaires pour effectuer cette action.', 403);
    }

    public static function planningNotFound(int $planningId): self
    {
        return new self("Le planning avec l'ID {$planningId} n'existe pas.", 404);
    }

    public static function cannotModifyFixedDay(): self
    {
        return new self('Ce type de jour ne peut pas être modifié.', 422);
    }
}
