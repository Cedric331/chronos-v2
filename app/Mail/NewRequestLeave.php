<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewRequestLeave extends Mailable
{
    use Queueable, SerializesModels;

    public $content;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($content)
    {
        $this->content = $content;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouvelle demande de congés sur Chronos',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.leave_paid',
            with: [
                'coordinateur_name' => $this->content['coordinateur_name'],
                'name' => $this->content['name'],
                'leave_type' => $this->content['leave_type'],
                'comment' => $this->content['comment'],
                'dates' => $this->content['dates'],
                'url' => $this->content['url']
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
