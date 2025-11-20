<?php

namespace App\Providers;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class EmailLogServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Intercepter les événements d'envoi d'emails
        Event::listen(MessageSending::class, function (MessageSending $event) {
            $message = $event->message;
            $to = [];

            // Récupérer les destinataires
            if (method_exists($message, 'getTo')) {
                $recipients = $message->getTo();

                foreach ($recipients as $recipient) {
                    if ($recipient instanceof \Symfony\Component\Mime\Address) {
                        $address = $recipient->getAddress();
                        $name = $recipient->getName();
                        $to[] = $name ? "$name <$address>" : $address;
                    }
                }
            }

            // Obtenir la trace d'appel
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 15);
            $relevantTrace = [];

            foreach ($trace as $call) {
                if (isset($call['file']) && ! str_contains($call['file'], 'vendor/laravel')) {
                    $relevantTrace[] = $call['file'].':'.($call['line'] ?? '?');
                }
            }

            // Journaliser les informations sur l'email
            Log::channel('email')->info('Email envoyé', [
                'to' => implode(', ', $to),
                'subject' => method_exists($message, 'getSubject') ? $message->getSubject() : 'N/A',
                'from' => $this->formatSender($message),
                'trace' => array_slice($relevantTrace, 0, 5),
            ]);
        });
    }

    public function register()
    {
        // Rien à faire ici, la configuration du canal est dans config/logging.php
    }

    /**
     * Formate l'expéditeur pour le log
     */
    private function formatSender($message)
    {
        if (! method_exists($message, 'getFrom')) {
            return 'N/A';
        }

        $from = $message->getFrom();
        $result = [];

        foreach ($from as $sender) {
            if ($sender instanceof \Symfony\Component\Mime\Address) {
                $address = $sender->getAddress();
                $name = $sender->getName();
                $result[] = $name ? "$name <$address>" : $address;
            }
        }

        return implode(', ', $result) ?: 'N/A';
    }
}
