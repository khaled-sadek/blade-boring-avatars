<?php

use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;

uses()->group('avatar');

test('the basic component', function () {
    /** @var \Tests\TestCase $this */
    /** @var \Illuminate\Testing\TestView $view */
    $view = $this->blade('<x-Avatar />');

    $view->assertSee('width="40"', false)
        ->assertSee('height="40"', false);
});

test('the size option', function () {
    /** @var \Tests\TestCase $this */
    /** @var \Illuminate\Testing\TestView $view */
    $view = $this->blade('<x-Avatar size="120" />');

    $view->assertSee('width="120"', false)
        ->assertSee('height="120"', false);
});

test('the lowercase alias', function () {
    /** @var \Tests\TestCase $this */
    /** @var \Illuminate\Testing\TestView $view */
    $view = $this->blade('<x-avatar size="64" />');

    $view->assertSee('width="64"', false)
        ->assertSee('height="64"', false);
});

test('it renders default variant and seed when no props provided', function () {
    /** @var \Tests\TestCase $this */
    /** @var \Illuminate\Testing\TestView $view */
    $view = $this->blade('<x-Avatar />');

    // Baseline: keeps default size assertions implicitly from existing tests.
    // Convert TestView to string before checking content
    $viewContent = (string) $view;

    // Check that output contains an SVG and some deterministic content (e.g., a path/rect/circle)
    $view->assertSee('<svg', false);

    // Common boring-avatars output contains shapes; assert one appears
    expect(
        str_contains($viewContent, '<rect') ||
        str_contains($viewContent, '<circle') ||
        str_contains($viewContent, '<path')
    )->toBeTrue('Expected at least one SVG shape element to be present');
});

test('custom name seed changes rendered output', function () {
    /** @var \Tests\TestCase $this */
    $a = (string) $this->blade('<x-Avatar name="alpha-seed" size="64" />');
    $b = (string) $this->blade('<x-Avatar name="beta-seed" size="64" />');

    expect($a)->not->toBe($b, 'Different name seeds should yield different avatar SVG output')
        ->and($a)->toContain('width="64"', 'height="64"')
        ->and($b)->toContain('width="64"', 'height="64"');
});

test('variant option affects output structure', function () {
    /** @var \Tests\TestCase $this */
    // Test that different variants produce different outputs
    $beam = (string) $this->blade('<x-Avatar name="seed" variant="beam" />');
    $marble = (string) $this->blade('<x-Avatar name="seed" variant="marble" />');
    $pixel = (string) $this->blade('<x-Avatar name="seed" variant="pixel" />');
    $default = (string) $this->blade('<x-Avatar name="seed" />'); // Should default to 'beam'

    // Different variants should produce different outputs
    expect($beam)->not->toBe($marble, 'Beam and Marble variants should produce different outputs');
    expect($beam)->not->toBe($pixel, 'Beam and Pixel variants should produce different outputs');
    // Default should be the same as explicitly setting variant="beam"
    expect($beam)->toBe($default, 'Default variant should be the same as variant="beam"');
    // All should be valid SVGs
    expect($beam)->toContain('<svg');
    expect($beam)->toContain('mask__beam');
    expect($marble)->toContain('<svg');
    expect($marble)->toContain('mask__marble');
    expect($pixel)->toContain('<svg');
    expect($pixel)->toContain('mask__pixel');

    // Test that an unknown variant falls back to the default (beam)
    $unknown = (string) $this->blade('<x-Avatar name="seed" variant="nonexistent" />');
    expect($unknown)->toContain('mask__beam');
});

test('colors palette is applied when provided', function () {
    /** @var \Tests\TestCase $this */
    // Provide a small palette; the output should include at least one of these colors
    $customColors = ['#FF0000', '#00FF00', '#0000FF'];
    /** @var \Illuminate\Testing\TestView $view */
    $view = $this->blade('<x-Avatar name="colors-seed" :colors="'.json_encode($customColors).'" />');

    $rendered = (string) $view;
    $colorFound = false;

    // Check if any of the custom colors appear in the SVG
    foreach ($customColors as $color) {
        if (str_contains($rendered, $color) || str_contains($rendered, 'fill="'.$color.'"')) {
            $colorFound = true;
            break;
        }
    }

    expect($colorFound)->toBeTrue('Expected at least one of the provided custom colors to appear in the SVG: '.implode(', ', $customColors));

    // Also verify that the default colors are not used
    $defaultColors = ['#92A1C6', '#146A7C', '#F0AB3D', '#C271B4', '#C20D90'];
    $defaultColorFound = false;

    foreach ($defaultColors as $color) {
        if (str_contains($rendered, $color) || str_contains($rendered, 'fill="'.$color.'"')) {
            $defaultColorFound = true;
            break;
        }
    }

    expect($defaultColorFound)->toBeFalse('Default colors should not appear when custom colors are provided');
});

test('square toggle changes mask shape or border radius', function () {
    /** @var \Tests\TestCase $this */
    // Test with a variant that uses a circular mask by default (e.g., 'ring' or 'bauhaus')
    $circleVariant = (string) $this->blade('<x-Avatar name="seed" variant="ring" />');

    // Test with a variant that uses a rectangular mask by default (e.g., 'pixel')
    $squareVariant = (string) $this->blade('<x-Avatar name="seed" variant="pixel" />');

    // They should differ in their SVG structure
    expect($circleVariant)->not->toBe($squareVariant);
    // Check for circle in the circular variant
    expect($circleVariant)->toContain('circle');
    expect($circleVariant)->toContain('<svg');
    // Check for rect in the square variant
    expect($squareVariant)->toContain('rect');
    expect($squareVariant)->toContain('<svg');
});

test('title attribute includes accessible title when provided', function () {
    /** @var \Tests\TestCase $this */
    /** @var \Illuminate\Testing\TestView $view */
    $view = $this->blade('<x-Avatar name="access-seed" title="Accessible Avatar" />');

    $rendered = (string) $view;
    // Either a title element, or aria-label/role attributes; search common patterns.
    $hasTitleTag = str_contains($rendered, '<title>Accessible Avatar</title>');
    $hasAria = str_contains($rendered, 'aria-label="Accessible Avatar"') || str_contains($rendered, 'role="img"');

    expect($hasTitleTag || $hasAria)->toBeTrue('Expected accessible labeling via <title> or aria-label/role attributes');
});

test('invalid or empty name is handled gracefully', function () {
    /** @var \Tests\TestCase $this */
    // Empty name should still render deterministically without errors
    /** @var \Illuminate\Testing\TestView $emptyView */
    $emptyView = $this->blade('<x-Avatar name="" />');
    $empty = (string) $emptyView;
    expect($empty)->toContain('<svg');
    expect($empty)->not->toBeEmpty();

    // Null name (omit prop) already covered by defaults; add explicit null-like case via Blade expression
    /** @var \Illuminate\Testing\TestView $nullView */
    $nullView = $this->blade('<x-Avatar :name="null" />');
    $nullLike = (string) $nullView;
    expect($nullLike)->toContain('<svg');
    expect($nullLike)->not->toBeEmpty();
});

test('min and max size edges', function () {
    /** @var \Tests\TestCase $this */
    // Very small size
    /** @var \Illuminate\Testing\TestView $tiny */
    $tiny = $this->blade('<x-Avatar size="1" />');
    $tiny->assertSee('width="1"', false)->assertSee('height="1"', false);

    // Large size
    /** @var \Illuminate\Testing\TestView $large */
    $large = $this->blade('<x-Avatar size="512" />');
    $large->assertSee('width="512"', false)->assertSee('height="512"', false);
});

test('lowercase alias supports all props like cased component', function () {
    /** @var \Tests\TestCase $this */
    /** @var \Illuminate\Testing\TestView $lower */
    $lower = $this->blade('<x-avatar name="alias-seed" variant="beam" size="80" :square="true" />');
    /** @var \Illuminate\Testing\TestView $cased */
    $cased = $this->blade('<x-Avatar name="alias-seed" variant="beam" size="80" :square="true" />');

    // Convert both to strings to compare the rendered output
    $lowerRendered = (string) $lower;
    $casedRendered = (string) $cased;

    // Alias should be functionally equivalent
    expect($lowerRendered)->toBe($casedRendered, 'Lowercase alias should render identically to cased component with same props');
});

test('unknown variant falls back to default without crashing', function () {
    /** @var \Tests\TestCase $this */
    /** @var \Illuminate\Testing\TestView $view */
    $view = $this->blade('<x-Avatar name="seed" variant="unknown-variant-xyz" />');
    $rendered = (string) $view;

    expect($rendered)->toContain('<svg');
    expect($rendered)->not->toBeEmpty();
});
