<?php

namespace Shaz3e\EmailBuilder\App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Shaz3e\EmailBuilder\App\Models\EmailTemplate;
use Shaz3e\EmailBuilder\App\Models\GlobalEmailTemplate;

class EmailTemplateMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;

    protected $template;

    protected $data;

    protected $globalHeader;

    protected $globalFooter;

    /**
     * Create a new message instance.
     */
    public function __construct($user, string $templateName, array $data = [])
    {
        $this->user = $user;
        $this->data = $data;
        $this->template = EmailTemplate::where('name', $templateName)->firstOrFail();

        // Preload global header and footer once
        $this->globalHeader = GlobalEmailTemplate::where('default_header', true)->value('header') ?? '';
        $this->globalFooter = GlobalEmailTemplate::where('default_footer', true)->value('footer') ?? '';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->parseContent($this->template->subject ?? 'No Subject')
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email-builder::emails.template',
            with: [
                'subject' => $this->parseContent($this->template->subject ?? 'No Subject'),
                'header' => $this->getHeader(),
                'body' => $this->parseContent($this->template->body ?? 'No Content'),
                'footer' => $this->getFooter(),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Resolve which header to use and replace placeholders.
     */
    protected function getHeader(): string
    {
        $header = $this->template->header ?? $this->globalHeader;

        return $this->parseContent($header);
    }

    /**
     * Resolve which footer to use and replace placeholders.
     */
    protected function getFooter(): string
    {
        $footer = $this->template->footer ?? $this->globalFooter;

        return $this->parseContent($footer);
    }

    /**
     * Replace placeholders inside the given content.
     */
    protected function parseContent(?string $content): string
    {
        if (is_null($content)) {
            return '';
        }

        $placeholders = $this->template->placeholders ?? []; // Already array, no decoding!

        $replacements = [];
        foreach ($placeholders as $placeholder) {
            $replacements['{'.$placeholder.'}'] = $this->data[$placeholder] ?? '';
        }

        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }
}
