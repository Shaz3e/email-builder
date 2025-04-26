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
     * Send an email using the template name.
     *
     * @param  mixed  $user  User object or email address
     */
    public function sendEmailByName($user, string $templateName, array $data = []): void
    {
        try {
            $template = EmailTemplate::where('name', $templateName)->firstOrFail();

            Mail::to($this->resolveEmail($user))
                ->send(new EmailTemplateMail($user, $templateName, $data));
        } catch (Exception $e) {
            Log::error("Email sending failed for template [$templateName]: ".$e->getMessage());
        }
    }

    /**
     * Resolve the email address from a User model or direct email string.
     */
    protected function resolveEmail($user): string
    {
        return is_object($user) && method_exists($user, 'getEmailForNotification')
            ? $user->getEmailForNotification()
            : (is_object($user) ? $user->email : $user);
    }
}
