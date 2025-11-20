<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExchangeRequestCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $exchangeRequests;

    public $requester;

    public $requested;

    public $comment;

    public $url;

    /**
     * Create a new message instance.
     */
    public function __construct(array $exchangeRequests, string $url)
    {
        $this->exchangeRequests = $exchangeRequests;

        // Tous les échanges ont le même demandeur et destinataire
        if (count($exchangeRequests) > 0) {
            $firstExchange = $exchangeRequests[0];
            $this->requester = $firstExchange->requester;
            $this->requested = $firstExchange->requested;
            $this->comment = $firstExchange->requester_comment;
        }

        $this->url = $url;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouvelle demande d\'échange de planning sur Chronos',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.exchange_request_created',
            with: [
                'exchangeRequests' => $this->exchangeRequests,
                'requester' => $this->requester,
                'requested' => $this->requested,
                'comment' => $this->comment,
                'url' => $this->url,
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
}
