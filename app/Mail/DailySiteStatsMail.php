<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailySiteStatsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $date,
        public int $viewsCount,
        public int $commentsCount,
        public array $topArticles = []
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Статистика сайта за '.$this->date,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.daily_site_stats',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
