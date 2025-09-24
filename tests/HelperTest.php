<?php

namespace Tests;

use KhaledSadek\BladeBoringAvatars\Helper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
    public function test_get_number_from_string(): void
    {
        $this->assertSame(0, Helper::getNumber(''));
        $this->assertSame(97, Helper::getNumber('a'));
        $this->assertSame(98, Helper::getNumber('b'));
    }

    #[DataProvider('getUnitProvider')]
    public function test_get_unit(int $number, int $range, int $index, int $expected): void
    {
        $this->assertSame($expected, Helper::getUnit($number, $range, $index));
    }

    public function test_get_unit_throws_exception_for_zero_range(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Range must not be zero');

        Helper::getUnit(5, 0);
    }

    /**
     * @return array<int, array<int>>
     */
    public static function getUnitProvider(): array
    {
        return [
            // Test basic positive ranges
            [5, 3, 0, 2],      // 5 % 3 = 2
            [7, 5, 0, 2],      // 7 % 5 = 2
            [10, 4, 0, 2],     // 10 % 4 = 2

            // Test negative numbers
            [-1, 3, 0, 2],     // (-1 % 3 + 3) % 3 = 2
            [-5, 4, 0, 3],     // (-5 % 4 + 4) % 4 = 3

            // Test with index that affects sign (when digit at index is even)
            [1234, 5, 2, -4],  // digit at index 2 is 2 (even), so negative of (1234 % 5) = 4
            [1235, 5, 2, 0],   // digit at index 2 is 2 (even), but (1235 % 5) = 0, so 0
            [1234, 5, 1, 4],   // digit at index 1 is 3 (odd), so positive (1234 % 5) = 4

            // Test negative ranges (should use absolute value)
            [5, -3, 0, 2],     // same as positive range 3
            [-5, -4, 0, 3],    // same as positive range 4
        ];
    }
}
