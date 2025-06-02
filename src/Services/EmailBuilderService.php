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
     * @param  array  $data  The data to create the email template with, must contain
     *                       the following keys: 'name', 'subject', 'header', 'body',
     *                       'footer', and optionally 'placeholders'
     * @return \Shaz3e\EmailBuilder\App\Models\EmailTemplate The newly created email template
     */
    public function addTemplate($data)
    {
        // Check if placeholders are provided, if not, set them as an empty array
        if (! isset($data['placeholders'])) {
            $data['placeholders'] = [];
        }

        // Create the email template
        return EmailTemplate::create($data);
    }

    /**
     * Update an existing email template.
     *
     * @param  int  $id  The ID of the template to update
     * @param  array  $data  The data to update the template with, must contain
     *                       the keys: 'name', 'subject', 'header', 'body',
     *                       'footer', and optionally 'placeholders'
     * @return \Shaz3e\EmailBuilder\App\Models\EmailTemplate The updated email template
     */
    public function editTemplate($id, $data)
    {
        // Find the email template by ID
        $template = EmailTemplate::findOrFail($id);

        // Only update placeholders if they’re explicitly passed in the update data
        // Prevents accidental overwrites of the placeholders
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
     * @param  int  $id  The ID of the email template to retrieve
     * @return \Shaz3e\EmailBuilder\App\Models\EmailTemplate The email template with the given ID
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no email template with the given ID exists
     */
    public function getTemplate($id)
    {
        // Find the email template by ID
        return EmailTemplate::findOrFail($id);
    }

    /**
     * Delete an email template by ID.
     *
     * @param  int  $id  The ID of the email template to delete
     * @return int The number of rows affected by the deletion
     */
    public function deleteTemplate($id)
    {
        // Delete the email template from the database
        return EmailTemplate::destroy($id);
    }

    /**
     * Retrieve all email templates.
     *
     * Fetches all email templates from the database and returns them as
     * a collection of EmailTemplate objects.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, \Shaz3e\EmailBuilder\App\Models\EmailTemplate>
     */
    public function allTemplates(): \Illuminate\Database\Eloquent\Collection
    {
        // Fetch all email templates from the database
        return EmailTemplate::all();
    }

    /**
     * Create a new global email template.
     *
     * This method creates a new global email template in the database.
     * A global email template is a template that is used as the default
     * template for all emails sent by the email builder.
     *
     * @param  array  $data  The data to create the template with, must contain
     *                       the keys: 'header', 'footer', and optionally
     *                       'default_header', 'default_footer'
     * @return \Shaz3e\EmailBuilder\App\Models\GlobalEmailTemplate The newly created global email template
     */
    public function addGlobalTemplate($data)
    {
        return GlobalEmailTemplate::create($data);
    }

    /**
     * Edit an existing global email template.
     *
     * @param  int  $id  The ID of the template to update
     * @param  array  $data  The data to update the template with, must contain
     *                       the keys: 'header', 'footer', and optionally
     *                       'default_header', 'default_footer'
     * @return \Shaz3e\EmailBuilder\App\Models\GlobalEmailTemplate The updated email template
     */
    public function editGlobalEmailTemplate($id, $data)
    {
        // Find the email template by ID
        $template = GlobalEmailTemplate::findOrFail($id);

        // Update the template with the provided data
        $template->update($data);

        // Return the updated template
        return $template;
    }

    /**
     * Retrieve a global email template by ID.
     *
     * This method finds a global email template by its ID and returns it.
     * If no template with the given ID exists, it throws a ModelNotFoundException.
     *
     * @param  int  $id  The ID of the global email template to retrieve
     * @return \Shaz3e\EmailBuilder\App\Models\GlobalEmailTemplate The global email template with the given ID
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no global email template with the given ID exists
     */
    public function viewGlobalEmailTemplate($id)
    {
        // Find the email template by ID
        return GlobalEmailTemplate::findOrFail($id);
    }

    /**
     * Convert placeholders from array to comma-separated string.
     *
     * This method takes an array of placeholders and converts it
     * into a comma-separated string. If the array is empty, it
     * returns an empty string.
     *
     * @param  array  $placeholders  The array of placeholders
     * @return string The converted string
     */
    public function convertPlaceholdersToString($placeholders)
    {
        return $placeholders ? implode(', ', $placeholders) : '';
    }

    /**
     * Send an email using the template key.
     *
     * This method sends an email using the template specified by the given key.
     * It takes an email address or a user object as the recipient and an optional
     * array of data to pass to the email template.
     *
     * @param  string  $key  The key of the template to use
     * @param  mixed  $toEmail  User object or email address
     * @param  array  $data  Optional data to pass to the email template
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no template with the given key exists
     */
    public function sendEmailBykey($key, $toEmail, $data = []): void
    {
        try {
            // Find the email template by key
            $template = EmailTemplate::where('key', $key)->firstOrFail();

            // Create the mailable instance
            $mailable = new EmailTemplateMail($key, $toEmail, $data);

            // Check config to decide whether to queue or send immediately
            if (config('email-builder.queue_emails')) {
                Mail::to($toEmail)->queue($mailable);
                if (config('email-builder.log_info')) {
                    Log::info("Email queued for template [$key] to [$toEmail]");
                }
            } else {
                Mail::to($toEmail)->send($mailable);
                if (config('email-builder.log_info')) {
                    Log::info("Email sent for template [$key] to [$toEmail]");
                }
            }

            // Log the email sending result
            if (config('email-builder.log_info')) {
                Log::info("Email sent for template [$key] to [$toEmail]");
            }
        } catch (Exception $e) {
            // Log the error if email sending fails
            if (config('email-builder.log_info')) {
                Log::error("Email sending failed for template [$key]: ".$e->getMessage());
            }
        }
    }
}
