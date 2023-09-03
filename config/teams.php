<?php

return [

    // Paramètre pour les teams
    'active' => true,
    'logo' => true,

    // Détermine les types de jours par défaut des teams lors de la création de ceux-ci
    'type_days_default' => [
        'Planifié',
        'Récup JF',
        'Congés Payés',
        'JF',
        'Maladie',
        'Repos',
        'Formation',
    ],

    // Détermine les types de jours à ne pas modifier
    'type_days_fix' => [
        'Récup JF',
        'Congés Payés',
        'JF',
        'Maladie',
        'Formation',
    ],

];
