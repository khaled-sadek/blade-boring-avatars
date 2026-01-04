<?php

use KhaledSadek\BladeBoringAvatars\Helper;

it('gets number from string', function () {
    expect(Helper::getNumber(''))->toBe(0)
        ->and(Helper::getNumber('a'))->toBe(97)
        ->and(Helper::getNumber('b'))->toBe(98);
});

it('gets unit correctly', function (int $number, int $range, int $index, int $expected) {
    expect(Helper::getUnit($number, $range, $index))->toBe($expected);
})->with([
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
]);

it('gets random element from array', function () {
    $array = ['a', 'b', 'c'];
    expect(Helper::getRandomElement(0, $array))->toBe('a')
        ->and(Helper::getRandomElement(1, $array))->toBe('b')
        ->and(Helper::getRandomElement(3, $array))->toBe('a'); // 3 % 3 = 0
});

it('gets digit from number', function () {
    expect(Helper::getDigit(12345, 0))->toBe(5)
        ->and(Helper::getDigit(12345, 1))->toBe(4)
        ->and(Helper::getDigit(12345, 4))->toBe(1)
        ->and(Helper::getDigit(12345, 5))->toBe(0) // Out of bounds
        ->and(Helper::getDigit(12345, -1))->toBe(0); // Negative index
});

it('gets boolean from number', function () {
    // 123
    // index 0 -> 3 (odd) -> false
    // index 1 -> 2 (even) -> true
    // index 2 -> 1 (odd) -> false
    expect(Helper::getBoolean(123, 0))->toBeFalse()
        ->and(Helper::getBoolean(123, 1))->toBeTrue()
        ->and(Helper::getBoolean(123, 2))->toBeFalse();
});

it('calculates contrast color', function () {
    expect(Helper::getContrast('#000000'))->toBe('white')
        ->and(Helper::getContrast('#FFFFFF'))->toBe('black')
        ->and(Helper::getContrast('000000'))->toBe('white') // Without hash
        ->and(Helper::getContrast('FFFFFF'))->toBe('black'); // Without hash
});

it('throws exception for zero range', function () {
    Helper::getUnit(5, 0);
})->throws(\InvalidArgumentException::class, 'Range must not be zero');
