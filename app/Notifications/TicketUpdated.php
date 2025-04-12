<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\TicketComment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketUpdated extends Notification
{
    use Queueable;

    protected $ticket;
    protected $comment;
    protected $action;
    protected $performedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket, $action, $performedBy, ?TicketComment $comment = null)
    {
        $this->ticket = $ticket;
        $this->action = $action;
        $this->performedBy = $performedBy;
        $this->comment = $comment;

        // Log pour débogage
        \Log::info('Notification TicketUpdated créée pour le ticket #' . $ticket->id);
        \Log::info('Action: ' . $action);
        \Log::info('Effectuée par: ' . $performedBy->name . ' (' . $performedBy->email . ')');
        if ($comment) {
            \Log::info('Commentaire ID: ' . $comment->id);
        }
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        \Log::info('Notification TicketUpdated via() appelée pour ' . $notifiable->email);
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        \Log::info('Notification TicketUpdated toMail() appelée pour ' . $notifiable->email);

        // Utiliser un template Markdown personnalisé pour éviter les problèmes de syntaxe
        return (new MailMessage)
            ->subject("[Ticket #{$this->ticket->id}] {$this->getSubject()}")
            ->markdown('emails.ticket_updated', [
                'subject' => $this->getSubject(),
                'introLine' => $this->getIntroLine(),
                'action' => $this->action,
                'comment' => $this->comment,
                'ticket' => $this->ticket,
                'performedBy' => $this->performedBy,
                'url' => url(route('tickets.show', $this->ticket->id)),
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'action' => $this->action,
            'performed_by' => $this->performedBy->id,
        ];
    }

    /**
     * Get the subject for the notification.
     */
    protected function getSubject(): string
    {
        return match ($this->action) {
            'comment' => "Nouveau commentaire sur le ticket",
            'status' => "Statut du ticket mis à jour",
            'update' => "Ticket mis à jour",
            'closed' => "Ticket fermé",
            'reopened' => "Ticket réouvert",
            default => "Mise à jour du ticket",
        };
    }

    /**
     * Get the intro line for the notification.
     */
    protected function getIntroLine(): string
    {
        return match ($this->action) {
            'comment' => "Un nouveau commentaire a été ajouté au ticket #{$this->ticket->id}.",
            'status' => "Le statut du ticket #{$this->ticket->id} a été mis à jour.",
            'update' => "Le ticket #{$this->ticket->id} a été mis à jour.",
            'closed' => "Le ticket #{$this->ticket->id} a été fermé.",
            'reopened' => "Le ticket #{$this->ticket->id} a été réouvert.",
            default => "Le ticket #{$this->ticket->id} a été mis à jour.",
        };
    }
}
