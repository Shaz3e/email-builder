<?php

namespace Shaz3e\EmailBuilder\App\Mail;

use Shaz3e\EmailBuilder\App\Models\EmailTemplate;
use Shaz3e\EmailBuilder\App\Models\GlobalEmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailTemplateMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;

    protected $template;

    protected $data;

    protected $header;

    protected $footer;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $templateName, array $data = [])
    {
        $this->user = $user;
        $this->template = EmailTemplate::where('name', $templateName)->first();
        $this->data = $data;

        // Fetch the global header and footer (if available)
        $this->header = GlobalEmailTemplate::where('default_header', true)->value('header');
        $this->footer = GlobalEmailTemplate::where('default_footer', true)->value('footer');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->template ? $this->replacePlaceholders($this->template->subject) : 'Default Subject',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.template',
            with: [
                'subject' => $this->replacePlaceholders($this->template->subject ?? 'Default Subject'),
                'header' => $this->replacePlaceholders($this->template->header ?? $this->header),
                'body' => $this->replacePlaceholders($this->template->body ?? 'Default Email Content'),
                'footer' => $this->replacePlaceholders($this->template->footer ?? $this->footer),
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

    /**
     * Replace placeholders in the email template.
     */
    private function replacePlaceholders(string $content): string
    {
        if (! $this->template) {
            return $content;
        }

        // Decode stored placeholders from database
        $placeholders = json_decode($this->template->placeholders, true) ?? [];

        // Generate replacement array
        $replace = [];
        foreach ($placeholders as $placeholder) {
            $replace['{'.$placeholder.'}'] = $this->data[$placeholder] ?? '';
        }

        // Replace placeholders with actual values
        return str_replace(array_keys($replace), array_values($replace), $content);
    }
}
