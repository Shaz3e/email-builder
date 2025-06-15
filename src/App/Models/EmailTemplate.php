<?php

namespace Shaz3e\EmailBuilder\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    /** @use HasFactory<\Database\Factories\EmailTemplateFactory> */
    use HasFactory;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'header_image',
        'header_text',
        'header_text_color',
        'header_background_color',

        'footer_image',
        'footer_text',
        'footer_text_color',
        'footer_background_color',
        'footer_bottom_image',

        'key',
        'name',
        'subject',
        'body',
        'placeholders',

        'header',
        'footer',
    ];

    /**
     * casts
     *
     * @var array
     */
    protected $casts = [
        'placeholders' => 'array',  // Automatically casts JSON to array
        'header' => 'boolean',
        'footer' => 'boolean',
    ];

    /**
     * Render the content with provided placeholders replaced.
     *
     * This method takes an array of data as an argument and uses it to replace placeholders
     * in the body of the email template. Placeholders are in the format of {{placeholder_name}}
     * and are case-sensitive. If a placeholder is not found in the data array, it is left unchanged.
     *
     * @param  array  $data  The data to replace placeholders in the body
     * @return string The rendered body of the email template
     */
    public function renderBody(array $data)
    {
        $body = $this->body;
        // Check if placeholders are set
        if (! empty($this->placeholders)) {
            // Loop through the placeholders
            foreach ($this->placeholders as $placeholder) {
                // Get the value for the placeholder from the data array
                $value = $data[$placeholder] ?? '';
                // Replace the placeholder with the value in the body
                $body = str_replace("{{{$placeholder}}}", $value, $body);
            }
        }

        // Return the rendered body
        return $body;
    }

    /**
     * Set the key attribute, converting it to a normalized format.
     *
     * This method transforms the input value by converting it to lowercase,
     * replacing spaces with underscores, removing consecutive underscores,
     * and converting all special character to underscores, and then
     * trimming leading/trailing underscores. This is done to ensure that
     * the key is always in a consistent format, regardless of the format of
     * the input value.
     *
     * @param  string  $value
     * @return void
     */
    public function setKeyAttribute($value)
    {
        $key = strtolower($value);

        // Replace any character that is not a-z, 0-9 with underscores
        $key = preg_replace('/[^a-z0-9]+/', '_', $key);

        // Trim leading/trailing underscores
        $key = trim($key, '_');

        // Assign to attribute
        $this->attributes['key'] = $key;
    }


    /**
     * Set the placeholders attribute, converting it to a normalized format.
     *
     * This method ensures that the value is always stored as a JSON array of
     * strings, regardless of the format of the input value. It also sanitizes
     * each placeholder by removing all characters except a-z, 0-9, and
     * underscores, and converting all spaces to underscores. Finally, it
     * removes all empty entries and removes duplicates.
     *
     * @param  string|array  $value
     * @return void
     */
    public function setPlaceholdersAttribute($value)
    {
        // If null, store an empty JSON array
        if (is_null($value)) {
            $this->attributes['placeholders'] = json_encode([]);

            return;
        }

        // Convert comma/space-separated string to array if needed
        if (is_string($value)) {
            $value = preg_split('/[\s,]+/', strtolower($value));
        }

        // Sanitize each placeholder
        $placeholders = array_map(function ($placeholder) {
            // Convert spaces to underscores
            $placeholder = str_replace(' ', '_', $placeholder);

            // Remove all characters except a-z, 0-9, and underscores
            return preg_replace('/[^a-z0-9_]/', '', $placeholder);
        }, $value);

        // Filter out empty entries and remove duplicates
        $placeholders = array_unique(array_filter($placeholders));

        // Store as JSON
        $this->attributes['placeholders'] = json_encode($placeholders);
    }
}
