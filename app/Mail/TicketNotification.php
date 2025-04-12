<?php

namespace App\Mail;

use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    public $action;
    public $performedBy;
    public $comment;
    public $subject;
    public $introLine;

    /**
     * Create a new message instance.
     */
    public function __construct(Ticket $ticket, string $action, User $performedBy, ?TicketComment $comment = null)
    {
        $this->ticket = $ticket;
        $this->action = $action;
        $this->performedBy = $performedBy;
        $this->comment = $comment;
        $this->subject = $this->getSubject();
        $this->introLine = $this->getIntroLine();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[Ticket #{$this->ticket->id}] {$this->getSubject()}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.ticket_updated',
            with: [
                'subject' => $this->getSubject(),
                'introLine' => $this->getIntroLine(),
                'action' => $this->action,
                'comment' => $this->comment,
                'ticket' => $this->ticket,
                'performedBy' => $this->performedBy,
                'url' => route('tickets.show', $this->ticket->id),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
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
