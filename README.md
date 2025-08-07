# Email Builder

A Laravel package for managing email templates with dynamic placeholders.

![Packagist Version](https://img.shields.io/packagist/v/shaz3e/email-builder)
![Packagist Downloads](https://img.shields.io/packagist/dt/shaz3e/email-builder)
![License](https://img.shields.io/packagist/l/shaz3e/email-builder)
![Laravel Version](https://img.shields.io/badge/laravel-12.x-blue)

## Introduction

Email Builder allows you to define and manage email templates directly from your dashboard using dynamic placeholders like {{ name }}. You can use it to send system-generated emails such as:

- Welcome emails
- Order confirmations
- Abandoned cart reminders

No need to write a new Mailable class or job each time — everything is managed dynamically and queueable, based on config.

Install via composer

```bash
composer require shaz3e/email-builder
```

#### Publishables

Publish views

```bash
php artisan vendor:publish --tag=email-builder-views
```

Publish config (Recommended)

```bash
php artisan vendor:publish --tag=email-builder-config
```

Publish migrations (Setup config file before migration)

```bash
php artisan vendor:publish --tag=email-builder-migrations
```

Config File

```php
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
    'queue_emails' => true, // or false

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
];

```

Example Usage Anywhere in Laravel

```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Shaz3e\EmailBuilder\App\Models\EmailTemplate;
use Shaz3e\EmailBuilder\Facades\EmailBuilder;

class EmailTemplateController extends Controller
{

    public function index()
    {
        return EmailBuilder::allTemplates();
    }

    public function create()
    {
        return view('email-builders.create');
    }

    public function store(Request $request)
    {
        // Validate incoming request
        $validated = $request->validate([
            // ...
        ]);

        // Ensure this method inserts the data correctly
        $email = EmailBuilder::addTemplate($validated);

        // Redirect to the index page after saving
        return redirect()->route('email-templates.show', $email);
    }

    public function show(string $id)
    {
        $email = EmailBuilder::getTemplate($id);

        return $email;
    }

    public function edit(string $id)
    {
        $email = EmailTemplate::find($id);

        $placeholders = EmailBuilder::convertPlaceholdersToString($email->placeholders);

        return view('email-builders.edit', compact('email', 'placeholders'));
    }

    public function update(Request $request, string $id)
    {
        // Validate incoming request
        $validated = $request->validate([
            // ...
        ]);

        $email = EmailBuilder::editTemplate($id, $validated);

        return redirect()->route('email-templates.show', $email);
    }

    public function destory(string $id)
    {
        EmailBuilder::deleteTemplate($id);

        return redirect()->route('email-templates.index');
    }
}
```

Use Request or Valdiation within Controller

```php
use App\Rules\ImageRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmailTemplateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     *
     * This method is called before the `rules` method and allows you to modify the
     * request data before it is validated.
     *
     * In this case, we are setting the 'header' and 'footer' fields to 1 or 0
     * based on whether they are present in the request, and we are also
     * sanitizing the 'key' field by converting it to lowercase and replacing
     * any non-alphanumeric characters with underscores.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'header' => $this->has('header') ? 1 : 0,
            'footer' => $this->has('footer') ? 1 : 0,
        ]);

        if ($this->has('key')) {
            $this->merge([
                'key' => $this->sanitizeKey($this->input('key')),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'header_image' => [
                'nullable',
                new ImageRule, // Create Image Rule
            ],
            'header_text' => ['nullable', 'string'],
            'header_text_color' => ['nullable', 'string'],
            'header_background_color' => ['nullable', 'string'],

            'footer_image' => [
                'nullable',
                new ImageRule, // Create Image Rule
            ],
            'footer_text' => ['nullable', 'string'],
            'footer_text_color' => ['nullable', 'string'],
            'footer_background_color' => ['nullable', 'string'],
            'footer_bottom_image' => [
                'nullable',
                new ImageRule, // Create Image Rule
            ],
            'key' => [
                'required',
                'string',
                'max:255',
                Rule::unique('email_templates', 'key')->ignore($this->email_template), // Use your route model binding
            ],
            'name' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'min:3', 'max:255'],
            'body' => ['required', 'string'],
            'placeholders' => ['nullable', 'string'],
            'header' => ['required', 'in:0,1'],
            'footer' => ['required', 'in:0,1'],
        ];
    }

    /**
     * Sanitize the given key by converting it to lowercase, replacing non-alphanumeric
     * characters with underscores, and trimming any leading or trailing underscores.
     *
     * @param string $value The key to sanitize.
     * @return string The sanitized key.
     */
    protected function sanitizeKey($value)
    {
        $key = strtolower($value);
        $key = preg_replace('/[^a-z0-9]+/', '_', $key);

        return trim($key, '_');
    }
}
```

Create Image Rule and take advantage of config/email-builder rules

```php
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class ImageRule implements ValidationRule
{
    protected int $maxSize; // in kilobytes (KB)

    /**
     * Create a new rule instance.
     *
     * @param  int  $maxSize  Maximum file size in kilobytes (default 2048 KB = 2MB)
     */
    public function __construct($maxSize = null)
    {
        $this->maxSize = $maxSize ?? config('email-builder.image.max_size');
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // change $attribute to human readable
        $attribute = str_replace('_', ' ', ucwords($attribute));

        // Check if it is a file and an instance of UploadedFile
        if (! $value instanceof UploadedFile) {
            $fail("The {$attribute} must be a valid file.");

            return;
        }

        // Check if the file is an image
        if (! str_starts_with($value->getMimeType(), 'image/')) {
            $fail("The {$attribute} must be an image.");

            return;
        }

        // Check allowed extensions
        $allowedExtensions = config('email-builder.image.allowed_extensions');
        $extension = strtolower($value->getClientOriginalExtension());

        if (! in_array($extension, $allowedExtensions)) {
            $fail("The {$attribute} must be a file of type: ".implode(', ', $allowedExtensions).'.');

            return;
        }

        // Check file size (UploadedFile::getSize() returns bytes)
        if ($value->getSize() / 1024 > $this->maxSize) {
            $fail("The {$attribute} must not be larger than {$this->maxSize} KB.");

            return;
        }
    }
}

```

```php
// Than use anywhere where you want to send email
// Create instance of emailBuilder
use Shaz3e\EmailBuilder\Services\EmailBuilderService;
$email = new EmailBuilderService;

$user = User::findOrFail(1); // Send this user an email

$verification_link = route('verification'); // use this route from routes

$email->sendEmailByKey('welcome_email', $user->email, [
    'app_name' => config('app.name'),
    'name' => $user->name,
    'app_url' => $verification_link,
]);
```

#### Contributing

- If you have any suggestions please let me know : https://github.com/Shaz3e/email-builder/pulls.
- Please help me improve code https://github.com/Shaz3e/email-builder/pulls

#### License

Email Builder with [S3 Dashboard](https://github.com/Shaz3e/S3-Dashboard) is licensed under the MIT license. Enjoy!

## Credit

- [Shaz3e](https://www.shaz3e.com) | [YouTube](https://www.youtube.com/@shaz3e) | [Facebook](https://www.facebook.com/shaz3e) | [Twitter](https://twitter.com/shaz3e) | [Instagram](https://www.instagram.com/shaz3e) | [LinkedIn](https://www.linkedin.com/in/shaz3e/)
- [Diligent Creators](https://www.diligentcreators.com) | [Facebook](https://www.facebook.com/diligentcreators) | [Instagram](https://www.instagram.com/diligentcreators/) | [Twitter](https://twitter.com/diligentcreator) | [LinkedIn](https://www.linkedin.com/company/diligentcreators/) | [Pinterest](https://www.pinterest.com/DiligentCreators/) | [YouTube](https://www.youtube.com/@diligentcreator) [TikTok](https://www.tiktok.com/@diligentcreators) | [Google Map](https://g.page/diligentcreators)

![GitHub commit activity](https://img.shields.io/github/commit-activity/m/shaz3e/email-builder)

![GitHub Stats](https://github-readme-stats.vercel.app/api?username=shaz3e&show_icons=true&count_private=true&theme=default)

![GitHub Contributions Graph](https://github-profile-summary-cards.vercel.app/api/cards/profile-details?username=shaz3e&theme=default)
