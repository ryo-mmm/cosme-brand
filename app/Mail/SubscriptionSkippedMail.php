<?php

namespace App\Mail;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionSkippedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Carbon $newDate,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '【LUMIÈRE BOTANIQUE】配送スキップを受け付けました');
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.subscription-skipped');
    }
}
