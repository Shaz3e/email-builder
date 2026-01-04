# Email Builder

A powerful Laravel package for managing dynamic email templates with placeholders, global layouts, and safe image handling — without creating new Mailables for every email.

![Packagist Version](https://img.shields.io/packagist/v/shaz3e/email-builder)
![Packagist Downloads](https://img.shields.io/packagist/dt/shaz3e/email-builder)
![License](https://img.shields.io/packagist/l/shaz3e/email-builder)
![Laravel Version](https://img.shields.io/badge/laravel-12.x-blue)

---

## Introduction

**Email Builder** allows you to manage email templates directly from your application database using dynamic placeholders such as `{name}`, `{app_url}`, etc.

It is designed for **real production systems**, where:

* Emails must remain valid even months later
* Images should not break historical emails
* Templates are managed dynamically (not hardcoded Mailables)
* Global headers and footers are reusable
* Multiple applications or tenants may exist

Typical use cases:

* Welcome emails
* Email verification
* Password reset
* Order confirmations
* System notifications

---

## Features

* Database-driven email templates
* Dynamic placeholders
* Optional global header & footer
* Inline CSS for maximum email-client compatibility
* Safe image handling (no broken historical emails)
* Optional image cleanup via Artisan command
* Queue or send instantly (configurable)
* No need to create new Mailable classes

---

## Installation

Install via Composer:

```bash
composer require shaz3e/email-builder
```

---

## Publish Package Assets

### Publish Views

```bash
php artisan vendor:publish --tag=email-builder-views
```

### Publish Config (Recommended)

```bash
php artisan vendor:publish --tag=email-builder-config
```

### Publish Migrations

> ⚠️ Configure the package **before** running migrations.

```bash
php artisan vendor:publish --tag=email-builder-migrations
php artisan migrate
```

---

## Quick Start

Send your first email in minutes.

```php
use Shaz3e\EmailBuilder\Services\EmailBuilderService;

$emailBuilder = new EmailBuilderService();

$emailBuilder->sendEmailByKey(
    'welcome_email',
    'user@example.com',
    [
        'name' => 'John Doe',
        'app_name' => config('app.name'),
        'app_url' => route('verification.notice'),
    ]
);
```

> Ensure the `welcome_email` template exists in the database.

---

## Core Concepts

### Email Templates

Stored in the database and identified by a unique `key`.

### Global Header & Footer

Reusable layout applied across templates when enabled.

### Placeholders

Dynamic variables like `{name}` replaced at send time.

### Images

Images are stored on disk and referenced via **absolute URLs** to ensure compatibility across all email clients.

---

## Configuration

`config/email-builder.php`

```php
return [

    'image' => [
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif'],
        'max_size' => 2048,
    ],

    'queue_emails' => false,

    'log_info' => false,

    'body_column_type' => 'longText',

    'image_cleanup' => [
        'enabled' => true,
        'retention_days' => 180,
        'disk' => 'public',

        'directories' => [
            'global' => [
                'images/email-builder/global-email/header-images',
                'images/email-builder/global-email/footer-images',
                'images/email-builder/global-email/footer-bottom-images',
            ],

            'templates' => [
                'images/email-builder/email-templates/header-images',
                'images/email-builder/email-templates/footer-images',
                'images/email-builder/email-templates/footer-bottom-images',
            ],
        ],
    ],
];
```

---

## Request Validation Example

```php
use App\Rules\ImageRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmailTemplateRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'use_global_header' => $this->boolean('use_global_header') ? 1 : 0,
            'use_global_footer' => $this->boolean('use_global_footer') ? 1 : 0,
        ]);
    }

    public function rules(): array
    {
        return [
            'use_global_header' => ['required', 'in:0,1'],
            'use_global_footer' => ['required', 'in:0,1'],

            'header_image' => ['nullable', 'required_if:use_global_header,0', new ImageRule],
            'header_text' => ['required_if:use_global_header,0', 'string'],
            'header_text_color' => ['required_if:use_global_header,0', 'string'],
            'header_background_color' => ['required_if:use_global_header,0', 'string'],

            'footer_image' => ['nullable', 'required_if:use_global_footer,0', new ImageRule],
            'footer_text' => ['required_if:use_global_footer,0', 'string'],
            'footer_text_color' => ['required_if:use_global_footer,0', 'string'],
            'footer_background_color' => ['required_if:use_global_footer,0', 'string'],

            'key' => ['required', 'string', Rule::unique('email_templates', 'key')],
            'name' => ['required', 'string'],
            'subject' => ['required', 'string'],
            'body' => ['required', 'string'],
        ];
    }
}
```

---

## Image Retention Policy

* Images are **never deleted automatically**
* Prevents broken historical emails
* Cleanup is explicit and controlled

### Cleanup Command

```bash
php artisan email-builder:cleanup-images
php artisan email-builder:cleanup-images --days=180
php artisan email-builder:cleanup-images --force
```

---

## Email Client Compatibility

Designed for:

* Gmail (web & mobile)
* Outlook (Windows & macOS)
* Yahoo Mail
* iCloud Mail
* Hotmail
* Custom webmail clients

Uses conservative HTML and inline CSS.

---

## Contributing

Pull requests are welcome.

GitHub: [https://github.com/Shaz3e/email-builder](https://github.com/Shaz3e/email-builder)

---

## License

Email Builder is licensed under the **MIT License**.

---

## Credits

* **Shaz3e** – [https://www.shaz3e.com](https://www.shaz3e.com)
* **Diligent Creators** – [https://www.diligentcreators.com](https://www.diligentcreators.com)

![GitHub commit activity](https://img.shields.io/github/commit-activity/m/shaz3e/email-builder)
