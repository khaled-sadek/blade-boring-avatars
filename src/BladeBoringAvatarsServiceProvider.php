<?php

namespace KhaledSadek\BladeBoringAvatars;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
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
        $this->callAfterResolving(BladeCompiler::class, function(BladeCompiler $blade){
            $blade->component(Avatar::class);
        });
    }
}
