<?php

namespace Shaz3e\EmailBuilder\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Shaz3e\EmailBuilder\App\Mail\EmailTemplateMail;
use Shaz3e\EmailBuilder\App\Models\EmailTemplate;
use Shaz3e\EmailBuilder\App\Models\GlobalEmailTemplate;

class EmailBuilderService
{
    /**
     * Create a new email template.
     *
     * @param  array  $data
     * @return \Shaz3e\EmailBuilder\App\Models\EmailTemplate
     */
    public function addTemplate($data)
    {
        // Check if placeholders are provided, if not, set them as an empty array
        if (! isset($data['placeholders'])) {
            $data['placeholders'] = [];
        }

        return EmailTemplate::create($data);
    }

    /**
     * Update an existing email template.
     *
     * @param  int  $id
     * @param  array  $data
     * @return \Shaz3e\EmailBuilder\App\Models\EmailTemplate
     */
    public function editTemplate($id, $data)
    {
        // Find the email template by ID
        $template = EmailTemplate::findOrFail($id);

        // Only update placeholders if they’re explicitly passed in the update data
        if (! array_key_exists('placeholders', $data)) {
            unset($data['placeholders']); // Do not overwrite placeholders if not passed
        }

        // Update the template with the provided data
        $template->update($data);

        // Return the updated template
        return $template;
    }

    /**
     * Fetch an email template by ID.
     *
     * @param  int  $id
     * @return \Shaz3e\EmailBuilder\App\Models\EmailTemplate
     */
    public function getTemplate($id)
    {
        // Find the email template by ID
        return EmailTemplate::findOrFail($id);
    }

    /**
     * Delete an email template by ID.
     *
     * @param  int  $id
     * @return int
     */
    public function deleteTemplate($id)
    {
        return EmailTemplate::destroy($id);
    }

    /**
     * Retrieve all email templates.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allTemplates()
    {
        // Fetch all email templates from the database
        return EmailTemplate::all();
    }

    public function addGlobalTemplate($data)
    {
        return GlobalEmailTemplate::create($data);
    }

    public function editGlobalEmailTemplate($id, $data)
    {
        // Find the email template by ID
        $template = GlobalEmailTemplate::findOrFail($id);

        // Update the template with the provided data
        $template->update($data);

        // Return the updated template
        return $template;
    }

    public function viewGlobalEmailTemplate($id)
    {
        // Find the email template by ID
        return GlobalEmailTemplate::findOrFail($id);
    }

    /**
     * Convert placeholders from array to comma-separated string.
     *
     * @param  array  $placeholders
     * @return string
     */
    public function convertPlaceholdersToString($placeholders)
    {
        return $placeholders ? implode(', ', $placeholders) : '';
    }

    /**
     * Send an email based on the template name.
     *
     * @param  string  $name
     * @param  string  $toEmail
     * @param  array  $data
     * @return void
     */
    public function sendEmailByName($name, $toEmail, $data = [])
    {
        try {
            // Fetch the email template by
            $template = EmailTemplate::where('name', $name)->firstOrFail();

            // Replace placeholders in the template content and subject
            $content = $this->replacePlaceholders($template->content, $data);
            $subject = $this->replacePlaceholders($template->subject, $data);

            // Send the email
            Mail::to($toEmail)->send(new EmailTemplateMail($subject, $content));
        } catch (Exception $e) {
            Log::error("Email sending failed for name $name: ".$e->getMessage());
        }
    }

    /**
     * Replace placeholders in the template content.
     *
     * @param  string  $template
     * @param  array  $data
     * @return string
     */
    protected function replacePlaceholders($template, $data)
    {
        return preg_replace_callback('/\{\{\s*(\w+)\s*\}\}/', function ($matches) use ($data) {
            $placeholder = $matches[1];

            return $data[$placeholder] ?? ''; // Default to empty string if placeholder not provided
        }, $template);
    }
}
