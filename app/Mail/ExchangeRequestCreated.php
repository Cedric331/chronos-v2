<?php

namespace App\Mail;

use App\Models\ExchangeRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExchangeRequestCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $exchangeRequest;
    public $requester;
    public $requested;
    public $requesterPlanning;
    public $requestedPlanning;

    /**
     * Create a new message instance.
     */
    public function __construct(ExchangeRequest $exchangeRequest)
    {
        $this->exchangeRequest = $exchangeRequest;
        $this->requester = $exchangeRequest->requester;
        $this->requested = $exchangeRequest->requested;
        $this->requesterPlanning = $exchangeRequest->requesterPlanning;
        $this->requestedPlanning = $exchangeRequest->requestedPlanning;
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
                'exchangeRequest' => $this->exchangeRequest,
                'requester' => $this->requester,
                'requested' => $this->requested,
                'requesterPlanning' => $this->requesterPlanning,
                'requestedPlanning' => $this->requestedPlanning,
                'url' => route('exchanges.show', $this->exchangeRequest->id),
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
