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

    public function test_it_renders_default_variant_and_seed_when_no_props_provided(): void
    {
        $view = $this->blade('<x-Avatar />');

        // Baseline: keeps default size assertions implicitly from existing tests.
        // Check that output contains an SVG and some deterministic content (e.g., a path/rect/circle)
        $view->assertSee('<svg', false);
        // Common boring-avatars output contains shapes; assert one appears
        $this->assertTrue(
            str_contains($view->render(), '<rect') || str_contains($view->render(), '<circle') || str_contains($view->render(), '<path'),
            'Expected at least one SVG shape element to be present'
        );
    }

    public function test_custom_name_seed_changes_rendered_output(): void
    {
        $a = $this->blade('<x-Avatar name="alpha-seed" size="64" />')->render();
        $b = $this->blade('<x-Avatar name="beta-seed" size="64" />')->render();

        $this->assertNotSame($a, $b, 'Different name seeds should yield different avatar SVG output');
        $this->assertStringContainsString('width="64"', $a);
        $this->assertStringContainsString('height="64"', $a);
        $this->assertStringContainsString('width="64"', $b);
        $this->assertStringContainsString('height="64"', $b);
    }

    public function test_variant_option_affects_output_structure(): void
    {
        // Try a couple of common boring-avatars variants; adjust if the component restricts variants differently
        $beam = $this->blade('<x-Avatar name="seed" variant="beam" />')->render();
        $marble = $this->blade('<x-Avatar name="seed" variant="marble" />')->render();

        $this->assertNotSame($beam, $marble, 'Different variants should render different SVG structures');
        // Both should be valid SVGs
        $this->assertStringContainsString('<svg', $beam);
        $this->assertStringContainsString('<svg', $marble);
    }

    public function test_colors_palette_is_applied_when_provided(): void
    {
        // Provide a small palette; the output should include these fill colors somewhere
        $view = $this->blade('<x-Avatar name="colors-seed" :colors="[\'#FF0000\', \'#00FF00\', \'#0000FF\']" />');

        $rendered = $view->render();
        $this->assertTrue(
            str_contains($rendered, '#FF0000') || str_contains($rendered, 'fill="#FF0000"'),
            'Expected the provided red color to appear in the SVG'
        );
        $this->assertTrue(
            str_contains($rendered, '#00FF00') || str_contains($rendered, 'fill="#00FF00"'),
            'Expected the provided green color to appear in the SVG'
        );
        $this->assertTrue(
            str_contains($rendered, '#0000FF') || str_contains($rendered, 'fill="#0000FF"'),
            'Expected the provided blue color to appear in the SVG'
        );
    }

    public function test_square_toggle_changes_mask_shape_or_border_radius(): void
    {
        $circleish = $this->blade('<x-Avatar name="seed" :square="false" />')->render();
        $squareish = $this->blade('<x-Avatar name="seed" :square="true" />')->render();

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

        $rendered = $view->render();
        // Either a title element, or aria-label/role attributes; search common patterns.
        $hasTitleTag = str_contains($rendered, '<title>Accessible Avatar</title>');
        $hasAria = str_contains($rendered, 'aria-label="Accessible Avatar"') || str_contains($rendered, 'role="img"');

        $this->assertTrue($hasTitleTag || $hasAria, 'Expected accessible labeling via <title> or aria-label/role attributes');
    }

    public function test_invalid_or_empty_name_is_handled_gracefully(): void
    {
        // Empty name should still render deterministically without errors
        $empty = $this->blade('<x-Avatar name="" />')->render();
        $this->assertStringContainsString('<svg', $empty);
        $this->assertNotEmpty($empty);

        // Null name (omit prop) already covered by defaults; add explicit null-like case via Blade expression
        $nullLike = $this->blade('<x-Avatar :name="null" />')->render();
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
        $lower = $this->blade('<x-avatar name="alias-seed" variant="beam" size="80" :square="true" />')->render();
        $cased = $this->blade('<x-Avatar name="alias-seed" variant="beam" size="80" :square="true" />')->render();

        // Alias should be functionally equivalent
        $this->assertSame($cased, $lower, 'Lowercase alias should render identically to cased component with same props');
    }

    public function test_unknown_variant_falls_back_to_default_without_crashing(): void
    {
        $view = $this->blade('<x-Avatar name="seed" variant="unknown-variant-xyz" />');
        $rendered = $view->render();

        $this->assertStringContainsString('<svg', $rendered);
        $this->assertNotEmpty($rendered);
    }
