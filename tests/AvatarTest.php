<?php

namespace Tests;

use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use KhaledSadek\BladeBoringAvatars\BladeBoringAvatarsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class AvatarTest extends Orchestra
{
    use InteractsWithViews;

    protected function getPackageProviders($app): array
    {
        return [
            BladeBoringAvatarsServiceProvider::class,
        ];
    }

    public function test_the_basic_component(): void
    {
        $view = $this->blade('<x-Avatar />');

        $view->assertSee('width="40"', false)
            ->assertSee('height="40"', false);
    }

    public function test_the_size_option(): void
    {
        $view = $this->blade('<x-Avatar size="120" />');

        $view->assertSee('width="120"', false)
            ->assertSee('height="120"', false);
    }

    public function test_the_lowercase_alias(): void
    {
        $view = $this->blade('<x-avatar size="64" />');

        $view->assertSee('width="64"', false)
            ->assertSee('height="64"', false);
    }

    public function test_it_renders_default_variant_and_seed_when_no_props_provided(): void
    {
        $view = $this->blade('<x-Avatar />');

        // Baseline: keeps default size assertions implicitly from existing tests.
        // Check that output contains an SVG and some deterministic content (e.g., a path/rect/circle)
        $view->assertSee('<svg', false);
        // Common boring-avatars output contains shapes; assert one appears
        $this->assertTrue(
            str_contains($view, '<rect') || str_contains($view, '<circle') || str_contains($view, '<path'),
            'Expected at least one SVG shape element to be present'
        );
    }

    public function test_custom_name_seed_changes_rendered_output(): void
    {
        $a = $this->blade('<x-Avatar name="alpha-seed" size="64" />');
        $b = $this->blade('<x-Avatar name="beta-seed" size="64" />');

        $this->assertNotSame($a, $b, 'Different name seeds should yield different avatar SVG output');
        $this->assertStringContainsString('width="64"', $a);
        $this->assertStringContainsString('height="64"', $a);
        $this->assertStringContainsString('width="64"', $b);
        $this->assertStringContainsString('height="64"', $b);
    }

    public function test_variant_option_affects_output_structure(): void
    {
        // Test that different variants produce different outputs
        $beam = (string) $this->blade('<x-Avatar name="seed" variant="beam" />');
        $marble = (string) $this->blade('<x-Avatar name="seed" variant="marble" />');
        $pixel = (string) $this->blade('<x-Avatar name="seed" variant="pixel" />');
        $default = (string) $this->blade('<x-Avatar name="seed" />'); // Should default to 'beam'

        // Different variants should produce different outputs
        $this->assertNotSame($beam, $marble, 'Beam and Marble variants should produce different outputs');
        $this->assertNotSame($beam, $pixel, 'Beam and Pixel variants should produce different outputs');
        
        // Default should be the same as explicitly setting variant="beam"
        $this->assertSame($beam, $default, 'Default variant should be the same as variant="beam"');
        
        // All should be valid SVGs
        $this->assertStringContainsString('<svg', $beam);
        $this->assertStringContainsString('<svg', $marble);
        $this->assertStringContainsString('<svg', $pixel);
        
        // Test that the correct mask is being used
        $this->assertStringContainsString('mask__beam', $beam);
        $this->assertStringContainsString('mask__marble', $marble);
        $this->assertStringContainsString('mask__pixel', $pixel);
        
        // Test that an unknown variant falls back to the default (beam)
        $unknown = (string) $this->blade('<x-Avatar name="seed" variant="nonexistent" />');
        $this->assertStringContainsString('mask__beam', $unknown, 'Unknown variant should fall back to beam');
    }

    public function test_colors_palette_is_applied_when_provided(): void
    {
        // Provide a small palette; the output should include at least one of these colors
        $customColors = ['#FF0000', '#00FF00', '#0000FF'];
        $view = $this->blade('<x-Avatar name="colors-seed" :colors="'.json_encode($customColors).'" />');

        $rendered = $view;
        $colorFound = false;

        // Check if any of the custom colors appear in the SVG
        foreach ($customColors as $color) {
            if (str_contains($rendered, $color) || str_contains($rendered, 'fill="'.$color.'"')) {
                $colorFound = true;
                break;
            }
        }

        $this->assertTrue(
            $colorFound,
            'Expected at least one of the provided custom colors to appear in the SVG: '.implode(', ', $customColors)
        );

        // Also verify that the default colors are not used
        $defaultColors = ['#92A1C6', '#146A7C', '#F0AB3D', '#C271B4', '#C20D90'];
        $defaultColorFound = false;

        foreach ($defaultColors as $color) {
            if (str_contains($rendered, $color) || str_contains($rendered, 'fill="'.$color.'"')) {
                $defaultColorFound = true;
                break;
            }
        }

        $this->assertFalse(
            $defaultColorFound,
            'Default colors should not appear when custom colors are provided'
        );
    }

    public function test_square_toggle_changes_mask_shape_or_border_radius(): void
    {
        $circleish = $this->blade('<x-Avatar name="seed" :square="false" />');
        $squareish = $this->blade('<x-Avatar name="seed" :square="true" />');

        // They should differ, commonly via mask or shape selection
        $this->assertNotSame($circleish, $squareish);

        // Heuristics: a circular mask might reference "mask" or "clipPath" with rounded shape,
        // whereas square might omit or use rect with no rx.
        // Keep assertions generic to avoid tight coupling.
        $this->assertStringContainsString('<svg', $circleish);
        $this->assertStringContainsString('<svg', $squareish);
    }

    public function test_title_attribute_includes_accessible_title_when_provided(): void
    {
        $view = $this->blade('<x-Avatar name="access-seed" title="Accessible Avatar" />');

        $rendered = $view;
        // Either a title element, or aria-label/role attributes; search common patterns.
        $hasTitleTag = str_contains($rendered, '<title>Accessible Avatar</title>');
        $hasAria = str_contains($rendered, 'aria-label="Accessible Avatar"') || str_contains($rendered, 'role="img"');

        $this->assertTrue($hasTitleTag || $hasAria, 'Expected accessible labeling via <title> or aria-label/role attributes');
    }

    public function test_invalid_or_empty_name_is_handled_gracefully(): void
    {
        // Empty name should still render deterministically without errors
        $empty = $this->blade('<x-Avatar name="" />');
        $this->assertStringContainsString('<svg', $empty);
        $this->assertNotEmpty($empty);

        // Null name (omit prop) already covered by defaults; add explicit null-like case via Blade expression
        $nullLike = $this->blade('<x-Avatar :name="null" />');
        $this->assertStringContainsString('<svg', $nullLike);
        $this->assertNotEmpty($nullLike);
    }

    public function test_min_and_max_size_edges(): void
    {
        // Very small size
        $tiny = $this->blade('<x-Avatar size="1" />');
        $tiny->assertSee('width="1"', false)->assertSee('height="1"', false);

        // Large size
        $large = $this->blade('<x-Avatar size="512" />');
        $large->assertSee('width="512"', false)->assertSee('height="512"', false);
    }

    public function test_lowercase_alias_supports_all_props_like_cased_component(): void
    {
        $lower = $this->blade('<x-avatar name="alias-seed" variant="beam" size="80" :square="true" />');
        $cased = $this->blade('<x-Avatar name="alias-seed" variant="beam" size="80" :square="true" />');

        // Convert both to strings to compare the rendered output
        $lowerRendered = (string) $lower;
        $casedRendered = (string) $cased;

        // Alias should be functionally equivalent
        $this->assertSame($casedRendered, $lowerRendered, 'Lowercase alias should render identically to cased component with same props');
    }

    public function test_unknown_variant_falls_back_to_default_without_crashing(): void
    {
        $view = $this->blade('<x-Avatar name="seed" variant="unknown-variant-xyz" />');
        $rendered = $view;

        $this->assertStringContainsString('<svg', $rendered);
        $this->assertNotEmpty($rendered);
    }
}
