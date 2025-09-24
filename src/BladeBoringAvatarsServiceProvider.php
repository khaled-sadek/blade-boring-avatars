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
     * Bootstrap services.
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
