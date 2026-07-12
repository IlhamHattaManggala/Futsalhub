<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FreeTrialExpiredMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $team;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $team)
    {
        $this->user = $user;
        $this->team = $team;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Masa Uji Coba Gratis Tim Anda Telah Berakhir - ' . config('app.name', 'FutsalHub'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.free_trial_expired',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
