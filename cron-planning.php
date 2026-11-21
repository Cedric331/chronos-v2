<?php

/*
|--------------------------------------------------------------------------
| Cron : génération du calendrier (hébergement mutualisé OVH)
|--------------------------------------------------------------------------
|
| Sur un mutualisé on ne peut pas lancer "php artisan planning:generate" :
| le cron OVH ne sait appeler qu'un fichier. Ce script appelle donc la
| commande directement, sans passer par le scheduler Laravel (qui exige
| un cron à la minute près, impossible ici).
|
| À planifier une fois par jour dans l'espace client OVH.
| La commande est idempotente : elle n'insère que les jours manquants,
| la relancer plusieurs fois ne crée pas de doublons.
|
*/

// Ce script ne doit jamais être exécutable depuis le web.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit(1);
}

define('LARAVEL_START', microtime(true));

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

try {
    $status = $kernel->call('planning:generate');
    $output = $kernel->output();
} catch (\Throwable $e) {
    $status = 1;
    $output = $e->getMessage();
}

$message = $status === 0
    ? '[cron-planning] OK - calendrier genere'
    : "[cron-planning] ECHEC (code {$status}) : {$output}";

// Visible dans le rapport de cron envoye par OVH...
echo $message.PHP_EOL;

// ...et trace dans storage/logs pour pouvoir verifier a posteriori.
try {
    Illuminate\Support\Facades\Log::log($status === 0 ? 'info' : 'error', $message);
} catch (\Throwable $e) {
    // Si meme le logger est HS, on ne masque pas le code de sortie.
}

// Code de sortie non nul = OVH signale la tache en erreur.
exit($status === 0 ? 0 : 1);
