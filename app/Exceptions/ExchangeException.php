<?php

namespace App\Exceptions;

use Exception;

class ExchangeException extends Exception
{
    public static function planningNotOwned(): self
    {
        return new self('Un des plannings sélectionnés ne vous appartient pas.', 403);
    }

    public static function planningNotOwnedByUser(int $userId): self
    {
        return new self("Un des plannings sélectionnés n'appartient pas à l'utilisateur sélectionné.", 403);
    }

    public static function exchangeRequestAlreadyExists(): self
    {
        return new self('Il existe déjà une demande d\'échange en cours pour l\'un des plannings sélectionnés.', 409);
    }

    public static function exchangeRequestNotFound(int $exchangeId): self
    {
        return new self("La demande d'échange avec l'ID {$exchangeId} n'existe pas.", 404);
    }

    public static function cannotModifyCompletedExchange(): self
    {
        return new self('Impossible de modifier une demande d\'échange terminée.', 422);
    }
}
