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
     * and trimming leading/trailing underscores. This is done to ensure that
     * the key is always in a consistent format, regardless of the format of
     * the input value.
     *
     * @param  string  $value
     * @return void
     */
    public function setKeyAttribute($value)
    {
        // Transform the name value to a normalized format
        $this->attributes['key'] = strtolower(
            // Replace multiple spaces with a single underscore
            preg_replace(
                ['/ +/', '/_+/', '/^_+|_+$/'], // Patterns to match
                ['_', '_', ''],                // Replacements for the patterns
                // Replace spaces with underscores
                str_replace(' ', '_', $value)
            )
        );
    }

    /**
     * Mutator for the placeholders attribute.
     *
     * This method formats the input value for storage in the 'placeholders' attribute.
     * If the value is null, it stores an empty JSON array. If the value is a string,
     * it is converted to lowercase, and spaces or commas are replaced to create an array.
     * Any empty values or duplicates are removed from the array before it is saved as JSON.
     *
     * @param  string|array|null  $value  The input value to be processed and stored.
     * @return void
     */
    public function setPlaceholdersAttribute($value)
    {
        // Check if the value is null
        if (is_null($value)) {
            // Assign an empty JSON array to the 'placeholders' attribute
            $this->attributes['placeholders'] = json_encode([]);

            return;
        }

        // If the value is a string, process it into an array
        if (is_string($value)) {
            // Convert the string to lowercase and split by comma or whitespace
            $value = preg_split('/[\s,]+/', strtolower($value));
        }

        // Filter out empty values and ensure unique entries
        $placeholders = array_unique(array_filter($value));

        // Store the processed array as a JSON string in the 'placeholders' attribute
        $this->attributes['placeholders'] = json_encode($placeholders);
    }
}
