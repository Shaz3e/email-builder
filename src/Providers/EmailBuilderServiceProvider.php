<?php

namespace Shaz3e\EmailBuilder\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Shaz3e\EmailBuilder\Services\EmailBuilderService;

class EmailBuilderServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../views', 'email-builder');
    }

    public function register()
    {
        $this->app->singleton(EmailBuilderService::class, function ($app) {
            return new EmailBuilderService;
        });

        // Register the facade if needed
        $this->app->alias(EmailBuilderService::class, 'emailbuilder');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                // Todo: Create Console Command
            ]);
        }

        // Publish assets conditionally
        $this->publishAssets();
    }

    protected function publishAssets()
    {
        // Views
        $this->publishes([
            __DIR__.'/../views' => resource_path('views/vendor/email-builder'),
        ], 'email-builder-views');

        // Config
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
     * Check if a migration with the given base name already exists in the migrations directory.
     *
     * @param  string  $migrationBaseName
     * @return bool
     */
    protected function migrationExists($migrationBaseName)
    {
        $migrationsPath = database_path('migrations');
        $files = File::glob($migrationsPath.'/*_'.$migrationBaseName.'.php');

        return ! empty($files);
    }
}
