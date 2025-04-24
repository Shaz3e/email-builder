<?php

namespace Shaz3e\EmailBuilder\Facades;

use Illuminate\Support\Facades\Facade;

class EmailBuilder extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'emailbuilder'; // This should match the binding in your ServiceProvider
    }

    // Make sure this method is available in the facade
    public static function convertPlaceholdersToString($placeholders)
    {
        return app('emailbuilder')->convertPlaceholdersToString($placeholders);
    }
}
