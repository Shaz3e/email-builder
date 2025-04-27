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

    protected $globalHeaderImage;

    protected $globalHeaderText;

    protected $globalHeaderTextColor;

    protected $globalHeaderBackgroundColor;

    protected $globalFooterImage;

    protected $globalFooterText;

    protected $globalFooterTextColor;

    protected $globalFooterBackgroundColor;

    protected $globalFooterBottomImage;

    protected array $placeholders = [];

    /**
     * Create a new message instance.
     *
     * @param  string  $templateKey  The name of the email template to use
     * @param  mixed  $user  The user to send the email to
     * @param  array  $data  The data to pass to the view
     */
    public function __construct(string $templateKey, $user, array $data = [])
    {
        $this->user = $user;
        $this->data = $data;
        $this->template = EmailTemplate::where('key', $templateKey)->firstOrFail();

        // Initialize placeholders once
        $this->initializePlaceholders();

        // Get global header and footer templates
        // If no default templates are found, use empty strings
        $this->globalHeaderImage = GlobalEmailTemplate::whereNotNull('header_image')->value('header_image');
        $this->globalHeaderText = GlobalEmailTemplate::whereNotNull('header_text')->value('header_text');
        $this->globalHeaderTextColor = GlobalEmailTemplate::whereNotNull('header_text_color')->value('header_text_color');
        $this->globalHeaderBackgroundColor = GlobalEmailTemplate::whereNotNull('header_background_color')->value('header_background_color');

        $this->globalFooterImage = GlobalEmailTemplate::whereNotNull('footer_image')->value('footer_image');
        $this->globalFooterText = GlobalEmailTemplate::whereNotNull('footer_text')->value('footer_text');
        $this->globalFooterTextColor = GlobalEmailTemplate::whereNotNull('footer_text_color')->value('footer_text_color');
        $this->globalFooterBackgroundColor = GlobalEmailTemplate::whereNotNull('footer_background_color')->value('footer_background_color');
        $this->globalFooterBottomImage = GlobalEmailTemplate::whereNotNull('footer_bottom_image')->value('footer_bottom_image');
    }

    /**
     * Initialize and normalize placeholders once.
     *
     * This method retrieves the placeholders from the email template,
     * ensuring they are properly formatted as an array of strings.
     */
    protected function initializePlaceholders(): void
    {
        // Retrieve placeholders from the template, defaulting to an empty array if not set
        $placeholders = $this->template->placeholders ?? [];

        // Decode JSON string placeholders into an array, if applicable
        if (is_string($placeholders)) {
            $placeholders = json_decode($placeholders, true) ?? [];
        }

        // Ensure placeholders are always stored as an array of strings
        $this->placeholders = is_array($placeholders) ? $placeholders : [];
    }

    /**
     * Get the message envelope.
     *
     * This method constructs the email envelope, which includes
     * metadata about the email such as the subject.
     *
     * @return Envelope The envelope containing email metadata.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            // Set the email subject, parsing any placeholders
            subject: $this->parseContent($this->template->subject ?? 'No Subject')
        );
    }

    /**
     * Get the message content definition.
     *
     * This method returns the content that should be rendered
     * when the email is sent. It uses the email-builder::emails.template
     * view and passes in the subject, header, body, and footer
     * as variables.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email-builder::emails.template',
            with: [
                'subject' => $this->parseContent($this->template->subject ?? 'No Subject'),

                'header_image' => $this->parseContent($this->template->header_image ?? $this->globalHeaderImage),
                'header_text' => $this->parseContent($this->template->header_text ?? $this->globalHeaderText),
                'header_text_color' => $this->parseContent($this->template->header_text_color ?? $this->globalHeaderTextColor),
                'header_background_color' => $this->parseContent($this->template->header_background_color ?? $this->globalHeaderBackgroundColor),

                'body' => $this->parseContent($this->template->body ?? 'No Content'),

                'footer_image' => $this->parseContent($this->template->header_image ?? $this->globalFooterImage),
                'footer_text' => $this->parseContent($this->template->header_text ?? $this->globalFooterText),
                'footer_text_color' => $this->parseContent($this->template->header_text_color ?? $this->globalFooterTextColor),
                'footer_background_color' => $this->parseContent($this->template->header_background_color ?? $this->globalFooterBackgroundColor),
                'footer_bottom_image' => $this->parseContent($this->template->header_background_color ?? $this->globalFooterBottomImage),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * This method is empty because attachments are not supported
     * by this package. In a future version, attachments may be
     * added, but for now, this method simply returns an empty
     * array.
     *
     * @return array An empty array of attachments
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Replace placeholders inside the given content.
     *
     * This method takes a string of content and replaces any
     * placeholders with the corresponding values from the
     * email data array. If a placeholder is not found, it
     * is left in the content unchanged.
     *
     * @param  string|null  $content  The content to parse
     * @return string The parsed content
     */
    protected function parseContent(?string $content): string
    {
        /**
         * If $content is empty, return an empty string.
         * This prevents accidentally returning a string with
         * placeholders that haven't been replaced.
         */
        if (empty($content)) {
            return '';
        }

        /**
         * Initialize an array to store the replacements.
         * The keys will be the placeholders to search for,
         * and the values will be the values to replace them with.
         */
        $replacements = [];

        /**
         * Loop through the placeholders and build the $replacements array.
         */
        foreach ($this->placeholders as $placeholder) {
            /**
             * If the placeholder is found in the data array,
             * replace it with the corresponding value. If not,
             * leave the placeholder unchanged.
             */
            $replacements['{'.$placeholder.'}'] = $this->data[$placeholder] ?? '';
        }

        /**
         * Use str_replace to replace the placeholders in $content
         * with the values from $replacements.
         */
        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }
}
