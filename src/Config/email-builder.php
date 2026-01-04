<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Image Upload Settings
    |--------------------------------------------------------------------------
    |
    | These options control the validation for image uploads. You can define
    | the allowed file extensions and the maximum file size (in kilobytes).
    | The default max size is 2048 KB, which equals 2 MB.
    |
    */
    'image' => [
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif'],
        'max_size' => 2048, // in KB (2MB)
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Emails
    |--------------------------------------------------------------------------
    |
    | This option determines whether emails should be queued or sent
    | immediately. When set to true, all emails will be queued and processed
    | by a queue worker. When false, emails will be sent instantly.
    |
    */
    'queue_emails' => false, // or false

    /*
    |--------------------------------------------------------------------------
    | Log Email Info
    |--------------------------------------------------------------------------
    |
    | Enable this option to log info-level messages when emails are sent or
    | queued successfully. Useful for debugging or monitoring email flow.
    |
    */
    'log_info' => false, // or false

    /*
    |--------------------------------------------------------------------------
    | Email Template Body Column Type
    |--------------------------------------------------------------------------
    |
    | This option defines the database column type used for storing the email
    | template body content. You may choose between 'text', 'longText', or
    | 'json' based on your expected content size and structure.
    |
    | Supported: "text", "longText", "json"
    |
    */
    'body_column_type' => 'longText',

    /*
    |--------------------------------------------------------------------------
    | Image Retention & Cleanup
    |--------------------------------------------------------------------------
    |
    | Controls how email images are retained and cleaned up.
    | Images are NEVER deleted automatically unless cleanup is enabled.
    |
    */

    'image_cleanup' => [

        // Master switch
        'enabled' => true,

        // Default retention period in days
        'retention_days' => 180,

        // Storage disk
        'disk' => 'public',

        /*
        |--------------------------------------------------------------------------
        | Image Directories
        |--------------------------------------------------------------------------
        |
        | Each directory is scanned independently.
        | Only images NOT referenced in DB AND older than retention_days are removed.
        |
        */

        'directories' => [

            // Global email layout
            'global' => [
                'images/email-builder/global-email/header-images',
                'images/email-builder/global-email/footer-images',
                'images/email-builder/global-email/footer-bottom-images',
            ],

            // Per-template images
            'templates' => [
                'images/email-builder/email-templates/header-images',
                'images/email-builder/email-templates/footer-images',
                'images/email-builder/email-templates/footer-bottom-images',
            ],
        ],
    ],
];
