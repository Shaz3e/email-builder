# Email Builder

A Laravel package for managing email templates with dynamic placeholders.

![Packagist Version](https://img.shields.io/packagist/v/shaz3e/email-builder)
![Packagist Downloads](https://img.shields.io/packagist/dt/shaz3e/email-builder)
![License](https://img.shields.io/packagist/l/shaz3e/email-builder)
![Laravel Version](https://img.shields.io/badge/laravel-12.x-blue)

Email Builder are pre-designed email messages that can be customized to fit your needs. They can be used to send automated emails, such as welcome emails, abandoned cart reminders, and order confirmations all emails will written in html and queueable mean there is no need to create additional jobs or mailable everytime for all your email and best thing is you can write your own email from dashboard and use template placeholders like `{{ name }}` in your email but you need to register placeholders in the specific email.

Before proceeding the installation steps please read the complete documention to take only the step which is necessary for your application. We suggest only publish config file as this may be necessary to manage prefix, routes and middleware and only publish views when you need to modify it. It built with livewire grid view which poll data only visible mode.

**Note**
This package is built for [S3 Dashboard](https://github.com/Shaz3e/S3-Dashboard) and require extra efforts to use with any laravel application.

Install via composer

```bash
composer require shaz3e/email-builder
```

#### Publisables

Publish config only

```bash
php artisan vendor:publish --tag=email-builder-config
```

Publish migrations only

```bash
php artisan vendor:publish --tag=email-builder-migrations
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
            'header' => 'nullable',
            'footer' => 'nullable',
            'name' => 'required|unique:email_templates,name',
            'subject' => 'required',
            'body' => 'required',
            'placeholders' => 'nullable|string',
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
            'header' => 'nullable',
            'footer' => 'nullable',
            'name' => 'required|unique:email_templates,name,'.$id,
            'subject' => 'required',
            'body' => 'required',
            'placeholders' => 'nullable|string',

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

```php
// Than use anywhere where you want to send email
// Create instance of emailBuilder
$emailBuilder = new EmailBuilder();

$user = User::findOrFail(1);

$verification_link = route('verification');

$emailBuilder->sendEmailByName('welcome_email', $user->email, [
    'name' => $user->name, // placeholder
    'verification_link' => $verification_link, // placeholder
    'app_name' => config('app.name'), // placeholder
]);
```


#### Contributing

* If you have any suggestions please let me know : https://github.com/Shaz3e/email-builder/pulls.
* Please help me improve code https://github.com/Shaz3e/email-builder/pulls

#### License
Email Builder with [S3 Dashboard](https://github.com/Shaz3e/S3-Dashboard) is licensed under the MIT license. Enjoy!

## Credit
* [Shaz3e](https://www.shaz3e.com) | [YouTube](https://www.youtube.com/@shaz3e) | [Facebook](https://www.facebook.com/shaz3e) | [Twitter](https://twitter.com/shaz3e) | [Instagram](https://www.instagram.com/shaz3e) | [LinkedIn](https://www.linkedin.com/in/shaz3e/)
* [Diligent Creators](https://www.diligentcreators.com) | [Facebook](https://www.facebook.com/diligentcreators) | [Instagram](https://www.instagram.com/diligentcreators/) | [Twitter](https://twitter.com/diligentcreator) | [LinkedIn](https://www.linkedin.com/company/diligentcreators/) | [Pinterest](https://www.pinterest.com/DiligentCreators/) | [YouTube](https://www.youtube.com/@diligentcreator) [TikTok](https://www.tiktok.com/@diligentcreators) | [Google Map](https://g.page/diligentcreators)

![GitHub commit activity](https://img.shields.io/github/commit-activity/m/shaz3e/email-builder)

![GitHub Stats](https://github-readme-stats.vercel.app/api?username=shaz3e&show_icons=true&count_private=true&theme=default)

![GitHub Contributions Graph](https://github-profile-summary-cards.vercel.app/api/cards/profile-details?username=shaz3e&theme=default)
