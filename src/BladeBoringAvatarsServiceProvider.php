<?php

namespace KhaledSadek\BladeBoringAvatars;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use KhaledSadek\BladeBoringAvatars\Components\Avatar;

class BladeBoringAvatarsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the package: load component views and register Blade component aliases.
     *
     * Loads view templates from the package's components directory under the
     * "blade-boring-avatars" namespace, and registers the Avatar Blade component
     * with both "Avatar" (PascalCase) and "avatar" (lowercase) aliases so it can
     * be used from Blade templates in either form.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views/components', 'blade-boring-avatars');
        // Register aliases for the Avatar component (PascalCase and lowercase)
        Blade::component('Avatar', Avatar::class);
        Blade::component('avatar', Avatar::class);
    }
}
