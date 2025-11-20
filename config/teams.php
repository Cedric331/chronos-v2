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
        'Congés sans solde',
        'CP Matin',
        'CP Après-midi',
        'Jour Férié',
        'Maladie',
        'Repos',
        'Formation',
    ],

    'type_days_off' => [
        'Récup JF',
        'Congés Payés',
        'CP Matin',
        'CP Après-midi',
        'Congés sans solde',
    ],

    // Détermine les types de jours à ne pas modifier
    'type_days_fix' => [
        'Récup JF',
        'Congés Payés',
        'CP Matin',
        'CP Après-midi',
        'Congés sans solde',
        'Jour Férié',
        'Maladie',
        'Formation',
    ],

];
