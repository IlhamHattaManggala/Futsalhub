<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReactivateAccountMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $reactivateUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $reactivateUrl)
    {
        $this->user = $user;
        $this->reactivateUrl = $reactivateUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reaktivasi Akun Tim Futsal Anda - ' . config('app.name', 'FutsalHub'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reactivate_account',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
