<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use KhaledSadek\BladeBoringAvatars\BladeBoringAvatarsServiceProvider;
use KhaledSadek\BladeBoringAvatars\Components\Avatar;
use Orchestra\Testbench\TestCase as Orchestra;

class BladeBoringAvatarsServiceProviderTest extends Orchestra
{
    /**
     * Note: Framework under test:
     * - PHPUnit with Orchestra Testbench to bootstrap Laravel and the package provider.
     */
    protected function getPackageProviders($_app)
    {
        return [
            BladeBoringAvatarsServiceProvider::class,
        ];
    }

    public function test_it_registers_avatar_component_alias_lowercase(): void
    {
        // Assert component can be resolved via lowercase alias registered by the provider.
        // Rendering ensures Blade registry is actually wired.
        $html = $this->renderBlade('<x-avatar name="john" />');
        $this->assertIsString($html);
        $this->assertNotSame('', trim($html), 'Expected component to render some output for lowercase alias.');
    }

    public function test_it_registers_avatar_component_alias_pascal_case(): void
    {
        // Assert component can be resolved via PascalCase alias registered by the provider.
        $html = $this->renderBlade('<x-Avatar name="jane" />');
        $this->assertIsString($html);
        $this->assertNotSame('', trim($html), 'Expected component to render some output for PascalCase alias.');
    }

    public function test_blade_aliases_map_to_avatar_class(): void
    {
        // Blade's component registration should include both aliases pointing to the Avatar class.
        // Different Laravel versions expose alias data differently; we try to be defensive.
        $compiler = Blade::getFacadeRoot();
        $aliases = [];

        if (method_exists($compiler, 'getClassComponentAliases')) {
            $aliases = $compiler->getClassComponentAliases();
            // In most versions, alias => class mapping.
            $this->assertIsArray($aliases);
            $this->assertArrayHasKey('avatar', $aliases);
            $this->assertSame(Avatar::class, $aliases['avatar']);
            // Some setups may normalize aliases to kebab/lowercase; check PascalCase too if present.
            if (array_key_exists('Avatar', $aliases)) {
                $this->assertSame(Avatar::class, $aliases['Avatar']);
            }
        } elseif (property_exists($compiler, 'classComponentAliases')) {
            $aliases = $compiler->classComponentAliases;
            $this->assertIsArray($aliases);
            $this->assertArrayHasKey('avatar', $aliases);
            $this->assertSame(Avatar::class, $aliases['avatar']);
            if (array_key_exists('Avatar', $aliases)) {
                $this->assertSame(Avatar::class, $aliases['Avatar']);
            }
        } else {
            // Fallback: attempt actual render to ensure alias works as a runtime behavior assertion.
            $this->assertNotSame('', trim($this->renderBlade('<x-avatar name="test" />')));
            $this->assertNotSame('', trim($this->renderBlade('<x-Avatar name="test" />')));
        }
    }

    public function test_views_are_loaded_from_package_namespace(): void
    {
        // The provider calls: loadViewsFrom(__DIR__.'/../resources/views/components', 'blade-boring-avatars');
        // We assert the namespace is registered by checking the view finder paths.
        $finder = View::getFinder();

        $paths = [];
        if (method_exists($finder, 'getHints')) {
            $paths = $finder->getHints()['blade-boring-avatars'] ?? [];
        } elseif (property_exists($finder, 'hints')) {
            $paths = $finder->hints['blade-boring-avatars'] ?? [];
        }

        $this->assertIsArray($paths, 'Expected view hints array for namespace blade-boring-avatars.');
        $this->assertNotEmpty($paths, 'Expected at least one view path to be registered for blade-boring-avatars.');
    }

    public function test_rendering_with_missing_required_attributes_is_handled_gracefully(): void
    {
        // Edge case: If the component expects props (e.g., "name"), rendering without them should not crash the app.
        // It may either render a default or empty output; we simply assert no exception and string output.
        $html = $this->renderBlade('<x-avatar />');
        $this->assertIsString($html);
    }

    public function test_unexpected_attributes_do_not_break_rendering(): void
    {
        // Edge/failure tolerance: passing unexpected attributes should not fatally error.
        $html = $this->renderBlade('<x-avatar unknown-attr="value" />');
        $this->assertIsString($html);
    }

    /**
     * Helper to render Blade strings via view engine.
     */
    private function renderBlade(string $blade): string
    {
        // Using Blade::render available in newer Laravel, else fallback to a temp view render.
        if (method_exists(Blade::class, 'render')) {
            return Blade::render($blade);
        }

        // Fallback: create a temporary file-based view and render.
        $tempViewName = '__temp__'.md5($blade.microtime(true));
        $tempPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.$tempViewName.'.blade.php';
        file_put_contents($tempPath, $blade);

        // Register temp path as a view location at runtime.
        $this->app['view']->addLocation(dirname($tempPath));
        try {
            return view($tempViewName)->render();
        } finally {
            @unlink($tempPath);
        }
    }
}
