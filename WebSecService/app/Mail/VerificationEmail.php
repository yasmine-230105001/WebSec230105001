<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;


class VerificationEmail extends Mailable
{
    private $link;
    private $name;

    public function __construct($link, $name) {
        $this->link = $link;
        $this->name = $name;
    }

    public function envelope(): Envelope {
        return new Envelope(subject: 'Email Verification');
    }

    public function content(): Content {
        return new Content(
            view: 'emails.verification',
            with: ['link' => $this->link, 'name' => $this->name],
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
