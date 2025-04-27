<?php

namespace Shaz3e\EmailBuilder\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Shaz3e\EmailBuilder\Services\EmailBuilderService;

class EmailBuilderServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * This method is called after all other service providers have registered,
     * meaning you have access to all other loaded service providers. This is
     * useful if you need to register events or listen for events from other
     * service providers.
     *
     * @return void
     */
    public function boot()
    {
        // Load the views needed by the package
        // The views are stored in the "resources/views" directory of the package
        // and are named "emails.template". This is the view that will be used
        // when sending emails with the package.
        $this->loadViewsFrom(__DIR__.'/../views', 'email-builder');
    }

    /**
     * Register the service provider.
     *
     * This method is responsible for the registration of the EmailBuilderService
     * within the application's service container. It ensures that the service
     * is available as a singleton, meaning the same instance will be used
     * throughout the application. It also registers an alias for convenience
     * and sets up any console commands related to the package.
     *
     * @return void
     */
    public function register()
    {
        // Register the email builder service as a singleton
        $this->app->singleton(EmailBuilderService::class, function ($app) {
            // Create and return a new instance of EmailBuilderService
            return new EmailBuilderService;
        });

        // Register the facade alias for EmailBuilderService
        $this->app->alias(EmailBuilderService::class, 'emailbuilder');

        // Register console commands if the application is running in console
        if ($this->app->runningInConsole()) {
            $this->commands([
                // Todo: Create Console Command
            ]);
        }

        // Conditionally publish assets required by the package
        $this->publishAssets();
    }

    /**
     * Publish the assets needed by the package.
     *
     * @return void
     */
    protected function publishAssets()
    {
        // Publish views
        $this->publishes([
            __DIR__.'/../views' => resource_path('views/vendor/email-builder'),
        ], 'email-builder-views');

        // Publish config
        $this->publishes([
            __DIR__.'/../config/email-builder.php' => config_path('email-builder.php'),
        ], 'email-builder-config');

        // Publish migrations only if they don't already exist
        $globalSettingsMigrationExists = $this->migrationExists('create_global_email_templates_table');
        $emailTemplatesMigrationExists = $this->migrationExists('create_email_templates_table');

        if (! $globalSettingsMigrationExists) {
            $this->publishes([
                __DIR__.'/../database/migrations/create_global_email_templates_table.php' => database_path('migrations/'.date('Y_m_d_His').'_create_global_email_templates_table.php'),
            ], 'email-builder-migrations');
        }

        if (! $emailTemplatesMigrationExists) {
            $this->publishes([
                __DIR__.'/../database/migrations/create_email_templates_table.php' => database_path('migrations/'.date('Y_m_d_His', strtotime('+1 second')).'_create_email_templates_table.php'),
            ], 'email-builder-migrations');
        }
    }

    /**
     * Determine if a migration file with the specified base name already exists.
     *
     * This method checks the migrations directory for any existing migration files
     * that match the given base name. It returns true if one or more matching files
     * are found, otherwise it returns false.
     *
     * @param  string  $migrationBaseName  The base name of the migration file to check for.
     * @return bool Returns true if a matching migration file exists; false otherwise.
     */
    protected function migrationExists($migrationBaseName)
    {
        // Define the path to the migrations directory
        $migrationsPath = database_path('migrations');

        // Search for files in the migrations directory that match the specified base name
        $files = File::glob($migrationsPath.'/*_'.$migrationBaseName.'.php');

        // Return true if any matching files are found, false otherwise
        return ! empty($files);
    }
}
