<?php

namespace App\Enums;

enum ExchangeRequestStatus: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';

    /**
     * Vérifie si le statut est en attente
     */
    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    /**
     * Vérifie si le statut est terminé (accepté ou rejeté)
     */
    public function isCompleted(): bool
    {
        return in_array($this, [self::ACCEPTED, self::REJECTED, self::CANCELLED]);
    }

    /**
     * Vérifie si le statut peut être modifié
     */
    public function canBeModified(): bool
    {
        return $this === self::PENDING;
    }

    /**
     * Récupère le label français du statut
     */
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'En attente',
            self::ACCEPTED => 'Accepté',
            self::REJECTED => 'Refusé',
            self::CANCELLED => 'Annulé',
        };
    }
}

