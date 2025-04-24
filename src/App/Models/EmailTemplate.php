<?php

namespace Shaz3e\EmailBuilder\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    /** @use HasFactory<\Database\Factories\EmailTemplateFactory> */
    use HasFactory;

    protected $fillable = [
        'header',
        'footer',
        'name',
        'subject',
        'body',
        'placeholders',
    ];

    protected $casts = [
        'placeholders' => 'array',  // Automatically casts JSON to array
    ];

    /**
     * Render the content with provided placeholders replaced.
     *
     * @return string
     */
    public function renderBody(array $data)
    {
        $body = $this->body;
        if (! empty($this->placeholders)) {
            foreach ($this->placeholders as $placeholder) {
                $value = $data[$placeholder] ?? '';
                $body = str_replace("{{{$placeholder}}}", $value, $body);
            }
        }

        return $body;
    }

    /**
     * Set the name attribute, converting it to a normalized format.
     *
     * This method transforms the input value by converting it to lowercase,
     * replacing spaces with underscores, removing consecutive underscores,
     * and trimming leading/trailing underscores.
     *
     * @param  string  $value
     * @return void
     */
    public function setNameAttribute($value)
    {
        // Transform the name value to a normalized format
        $this->attributes['name'] = strtolower(
            preg_replace(
                ['/ +/', '/_+/', '/^_+|_+$/'], // Patterns to match
                ['_', '_', ''],                // Replacements for the patterns
                str_replace(' ', '_', $value)  // Replace spaces with underscores
            )
        );
    }

    /**
     * Mutator for the placeholders attribute.
     *
     * This method takes the input value and formats it appropriately for storage.
     * If the value is a string, it is converted to lowercase, spaces are replaced with commas,
     * and the resulting string is exploded into an array. The array is then filtered to remove
     * any empty values, and the resulting array is saved as JSON in the 'placeholders' attribute.
     *
     * @param  string|array  $value
     */
    public function setPlaceholdersAttribute($value)
    {
        if (is_null($value)) {
            $this->attributes['placeholders'] = json_encode([]);

            return;
        }

        if (is_string($value)) {
            // Convert to lowercase and split by comma or whitespace
            $value = preg_split('/[\s,]+/', strtolower($value));
        }

        // Remove empty values and duplicates
        $placeholders = array_unique(array_filter($value));

        $this->attributes['placeholders'] = json_encode($placeholders);
    }
}
