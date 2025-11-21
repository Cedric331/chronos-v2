<?php

namespace App\Enums;

enum PlanningType: string
{
    case PLANNED = 'Planifié';
    case REST = 'Repos';
    case PAID_LEAVE = 'Congés Payés';
    case UNPAID_LEAVE = 'Congés sans solde';
    case MORNING_PAID_LEAVE = 'CP Matin';
    case AFTERNOON_PAID_LEAVE = 'CP Après-midi';
    case HOLIDAY = 'Jour Férié';
    case SICK_LEAVE = 'Maladie';
    case TRAINING = 'Formation';
    case RECUP_HOLIDAY = 'Récup JF';

    /**
     * Vérifie si ce type nécessite des heures de travail
     */
    public function requiresHours(): bool
    {
        return match ($this) {
            self::PLANNED,
            self::TRAINING,
            self::MORNING_PAID_LEAVE,
            self::AFTERNOON_PAID_LEAVE => true,
            default => false,
        };
    }

    /**
     * Vérifie si ce type est un jour fixe (non modifiable)
     */
    public function isFixed(): bool
    {
        return match ($this) {
            self::HOLIDAY,
            self::PAID_LEAVE,
            self::MORNING_PAID_LEAVE,
            self::AFTERNOON_PAID_LEAVE,
            self::UNPAID_LEAVE,
            self::RECUP_HOLIDAY,
            self::SICK_LEAVE,
            self::TRAINING => true,
            default => false,
        };
    }

    /**
     * Vérifie si ce type est un jour de repos
     */
    public function isRestDay(): bool
    {
        return $this === self::REST;
    }

    /**
     * Vérifie si ce type est un jour de congé
     */
    public function isLeaveDay(): bool
    {
        return match ($this) {
            self::PAID_LEAVE,
            self::UNPAID_LEAVE,
            self::MORNING_PAID_LEAVE,
            self::AFTERNOON_PAID_LEAVE,
            self::RECUP_HOLIDAY => true,
            default => false,
        };
    }

    /**
     * Récupère tous les types de jours par défaut
     */
    public static function defaultTypes(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Récupère les types de jours fixes
     */
    public static function fixedTypes(): array
    {
        return array_filter(
            self::cases(),
            fn ($case) => $case->isFixed()
        );
    }

    /**
     * Récupère les types de jours qui nécessitent des heures
     */
    public static function typesRequiringHours(): array
    {
        return array_filter(
            self::cases(),
            fn ($case) => $case->requiresHours()
        );
    }
}
