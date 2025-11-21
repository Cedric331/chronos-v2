<?php

namespace App\Exceptions;

use Exception;

class TeamException extends Exception
{
    public static function teamNotFound(int $teamId): self
    {
        return new self("L'équipe avec l'ID {$teamId} n'existe pas.", 404);
    }

    public static function userNotInTeam(): self
    {
        return new self('L\'utilisateur n\'appartient pas à cette équipe.', 403);
    }

    public static function insufficientTeamPermissions(): self
    {
        return new self('Vous n\'avez pas les permissions nécessaires pour gérer cette équipe.', 403);
    }
}
