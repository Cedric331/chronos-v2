<?php

namespace App\Enums;

enum PaidLeaveStatus: string
{
    case PENDING = 'En attente';
    case ACCEPTED = 'Accepté';
    case REFUSED = 'Refusé';

    /**
     * Vérifie si le statut est en attente
     */
    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    /**
     * Vérifie si le statut est accepté
     */
    public function isAccepted(): bool
    {
        return $this === self::ACCEPTED;
    }

    /**
     * Vérifie si le statut est refusé
     */
    public function isRefused(): bool
    {
        return $this === self::REFUSED;
    }

    /**
     * Vérifie si le statut est terminé (accepté ou refusé)
     */
    public function isCompleted(): bool
    {
        return in_array($this, [self::ACCEPTED, self::REFUSED]);
    }
}

