<?php

namespace Shaz3e\EmailBuilder\Facades;

use Illuminate\Support\Facades\Facade;

class EmailBuilder extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * This method is the standard way to get the name of the accessor
     * in the Laravel Facade class.
     *
     * @return string The name of the accessor
     */
    protected static function getFacadeAccessor()
    {
        // This should match the binding in your ServiceProvider
        // that is used to register the service.
        return 'emailbuilder';
    }

    /**
     * Send an email using a template name.
     *
     * This method sends an email to the specified user by utilizing
     * the email template associated with the given template name.
     * Additional data can be passed to the email template.
     *
     * @param  mixed  $user  The recipient of the email
     * @param  string  $templateName  The name of the template to use
     * @param  array  $data  Additional data to pass to the template
     * @return mixed The result of the email sending operation
     */
    public static function sendEmailByName($user, string $templateName, array $data = [])
    {
        return app('emailbuilder')->sendEmailByName($user, $templateName, $data);
    }

    /**
     * Make sure this method is available in the facade
     *
     * This method takes an array of placeholders and converts them to a string
     * in the format of {{placeholder_1, placeholder_2, ...}}
     *
     * @param  array  $placeholders  The array of placeholders to convert
     * @return string The converted string
     */
    public static function convertPlaceholdersToString($placeholders)
    {
        return app('emailbuilder')->convertPlaceholdersToString($placeholders);
    }
}
