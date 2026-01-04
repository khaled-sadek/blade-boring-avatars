<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use KhaledSadek\BladeBoringAvatars\Components\Avatar;

test('it registers avatar component alias lowercase', function () {
    // Assert component can be resolved via lowercase alias registered by the provider.
    // Rendering ensures Blade registry is actually wired.
    $html = Blade::render('<x-avatar name="john" />');
    expect(trim($html))->not->toBe('', 'Expected component to render some output for lowercase alias.');
});

test('it registers avatar component alias pascal case', function () {
    // Assert component can be resolved via PascalCase alias registered by the provider.
    $html = Blade::render('<x-Avatar name="jane" />');
    expect(trim($html))->not->toBe('', 'Expected component to render some output for PascalCase alias.');
});

test('blade aliases map to avatar class', function () {
    // Blade's component registration should include both aliases pointing to the Avatar class.
    // Different Laravel versions expose alias data differently; we try to be defensive.
    $compiler = Blade::getFacadeRoot();
    /** @var \Illuminate\View\Compilers\BladeCompiler $compiler */
    expect($compiler)->toBeObject();
    $aliases = [];

    if (method_exists($compiler, 'getClassComponentAliases')) {
        $aliases = $compiler->getClassComponentAliases();
        // In most versions, alias => class mapping.
        expect($aliases)->toBeArray()
            ->toHaveKey('avatar')
            ->and($aliases['avatar'])->toBe(Avatar::class);

        // Some setups may normalize aliases to kebab/lowercase; check PascalCase too if present.
        if (array_key_exists('Avatar', $aliases)) {
            expect($aliases['Avatar'])->toBe(Avatar::class);
        }
    } else {
        // Access protected property via closure binding
        $aliases = (function () {
            return $this->classComponentAliases;
        })->call($compiler);
        
        /** @var array<string, mixed> $aliases */
        expect($aliases)->toBeArray();
        assert(is_array($aliases));
        expect($aliases)->toHaveKey('avatar');
        expect($aliases['avatar'])->toBe(Avatar::class);

        if (array_key_exists('Avatar', $aliases)) {
            expect($aliases['Avatar'])->toBe(Avatar::class);
        }
    }
    // Fallback: attempt actual render to ensure alias works as a runtime behavior assertion.
    expect(trim(Blade::render('<x-avatar name="test" />')))->not->toBe('');
    expect(trim(Blade::render('<x-Avatar name="test" />')))->not->toBe('');
});

test('views are loaded from package namespace', function () {
    // The provider calls: loadViewsFrom(__DIR__.'/../resources/views/components', 'blade-boring-avatars');
    // We assert the namespace is registered by checking the view finder paths.
    $finder = View::getFinder();

    $paths = [];
    if (method_exists($finder, 'getHints')) {
        $hints = (array) $finder->getHints();
        $paths = $hints['blade-boring-avatars'] ?? [];
    } elseif (property_exists($finder, 'hints')) {
        $hints = (array) $finder->hints;
        $paths = $hints['blade-boring-avatars'] ?? [];
    }
    
    /** @var array<int, string> $paths */
    expect($paths)->toBeArray('Expected view hints array for namespace blade-boring-avatars.');
    expect($paths)->not->toBeEmpty('Expected at least one view path to be registered for blade-boring-avatars.');
});

test('rendering with missing required attributes is handled gracefully', function () {
    // Edge case: If the component expects props (e.g., "name"), rendering without them should not crash the app.
    // It may either render a default or empty output; we simply assert no exception and string output.
    expect(function () {
        Blade::render('<x-avatar />');
    })->not->toThrow(\Throwable::class);
});

test('unexpected attributes do_not break rendering', function () {
    // Edge/failure tolerance: passing unexpected attributes should not fatally error.
    expect(function () {
        Blade::render('<x-avatar unknown-attr="value" />');
    })->not->toThrow(\Throwable::class);
});
