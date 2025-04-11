<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CheckEmailSending extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:check {--days=1 : Nombre de jours à analyser}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyse les logs d\'emails pour identifier les modèles d\'envoi';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $this->info("Analyse des emails envoyés au cours des {$days} derniers jours...");

        // Vérifier si le fichier de log existe
        $logPath = storage_path('logs/emails.log');
        if (!File::exists($logPath)) {
            $this->error("Le fichier de log des emails n'existe pas encore. Attendez que des emails soient envoyés.");
            return 1;
        }

        // Lire le fichier de log
        $logContent = File::get($logPath);
        $lines = explode("\n", $logContent);
        $emailLogs = [];
        $pattern = '/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\].*Email envoyé/';

        // Filtrer les lignes pertinentes
        $cutoffDate = now()->subDays($days);
        foreach ($lines as $line) {
            if (preg_match($pattern, $line, $matches)) {
                $logDate = \Carbon\Carbon::parse($matches[1]);
                if ($logDate->greaterThanOrEqualTo($cutoffDate)) {
                    $emailLogs[] = $line;
                }
            }
        }

        // Analyser les résultats
        $this->info("Nombre total d'emails envoyés: " . count($emailLogs));

        // Analyser par heure
        $emailsByHour = [];
        foreach ($emailLogs as $log) {
            if (preg_match($pattern, $log, $matches)) {
                $hour = substr($matches[1], 11, 2);
                if (!isset($emailsByHour[$hour])) {
                    $emailsByHour[$hour] = 0;
                }
                $emailsByHour[$hour]++;
            }
        }

        // Afficher les résultats par heure
        $this->info("\nDistribution des emails par heure:");
        ksort($emailsByHour);
        foreach ($emailsByHour as $hour => $count) {
            $this->line("  {$hour}h: {$count} emails");
        }

        // Analyser les destinataires les plus fréquents
        $recipients = [];
        $subjects = [];
        $sources = [];

        foreach ($emailLogs as $log) {
            // Extraire les destinataires
            if (preg_match('/"to":"([^"]+)"/', $log, $matches)) {
                $to = $matches[1];
                if (!isset($recipients[$to])) {
                    $recipients[$to] = 0;
                }
                $recipients[$to]++;
            }

            // Extraire les sujets
            if (preg_match('/"subject":"([^"]+)"/', $log, $matches)) {
                $subject = $matches[1];
                if (!isset($subjects[$subject])) {
                    $subjects[$subject] = 0;
                }
                $subjects[$subject]++;
            }

            // Extraire les sources (fichiers)
            if (preg_match('/"trace":\["([^"]+)"/', $log, $matches)) {
                $source = $matches[1];
                if (!isset($sources[$source])) {
                    $sources[$source] = 0;
                }
                $sources[$source]++;
            }
        }

        // Afficher les destinataires les plus fréquents
        arsort($recipients);
        $this->info("\nDestinataires les plus fréquents:");
        $i = 0;
        foreach ($recipients as $recipient => $count) {
            $this->line("  {$recipient}: {$count} emails");
            $i++;
            if ($i >= 5) break;
        }

        // Afficher les sujets les plus fréquents
        arsort($subjects);
        $this->info("\nSujets les plus fréquents:");
        $i = 0;
        foreach ($subjects as $subject => $count) {
            $this->line("  {$subject}: {$count} emails");
            $i++;
            if ($i >= 5) break;
        }

        // Afficher les sources les plus fréquentes
        arsort($sources);
        $this->info("\nSources les plus fréquentes:");
        $i = 0;
        foreach ($sources as $source => $count) {
            $this->line("  {$source}: {$count} emails");
            $i++;
            if ($i >= 5) break;
        }

        return 0;
    }
}
