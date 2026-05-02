<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminAlertMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $alertTitle,
        public readonly array  $context,
    ) {}

    public function envelope(): Envelope
    {
        $env = app()->environment();
        return new Envelope(
            subject: "[CRITICAL: {$env}] {$this->alertTitle}"
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.admin-alert');
    }
}
