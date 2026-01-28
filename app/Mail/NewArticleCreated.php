<?php

namespace App\Mail;

use App\Models\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewArticleCreated extends Mailable
{
    use Queueable, SerializesModels;

    public Article $article;

    /**
     * Create a new message instance.
     */
    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this
            ->subject('Новая статья: ' . $this->article->title)
            ->view('emails.articles.created', [
                'article' => $this->article,
            ]);
    }
}
