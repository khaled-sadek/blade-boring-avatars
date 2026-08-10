<?php

namespace Tests;

use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use KhaledSadek\BladeBoringAvatars\BladeBoringAvatarsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

/**
 * @method \Illuminate\Testing\TestView blade(string $template, array<string, mixed> $data = [])
 */
abstract class TestCase extends Orchestra
{
    use InteractsWithViews;

    protected function getPackageProviders($app): array
    {
        return [
            BladeBoringAvatarsServiceProvider::class,
        ];
    }
}
