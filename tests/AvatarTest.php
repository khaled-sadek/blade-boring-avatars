<?php

namespace Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use KhaledSadek\BladeBoringAvatars\Components\Avatar;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use KhaledSadek\BladeBoringAvatars\BladeBoringAvatarsServiceProvider;

class AvatarTest extends Orchestra
{
    use InteractsWithViews;

    protected function getPackageProviders($app)
    {
        return [
            BladeBoringAvatarsServiceProvider::class,
        ];
    }

    public function test_the_basic_component()
    {
        $view = $this->blade('<x-Avatar />');
    
        $view->assertSee('width="40"', false)
            ->assertSee('height="40"', false);
    }

    public function test_the_size_option()
    {
        $view = $this->blade('<x-Avatar size="120" />');
    
        $view->assertSee('width="120"', false)
            ->assertSee('height="120"', false);
    }

    public function test_the_lowercase_alias()
    {
        $view = $this->blade('<x-avatar size="64" />');

        $view->assertSee('width="64"', false)
            ->assertSee('height="64"', false);
    }
}
