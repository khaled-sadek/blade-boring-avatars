<?php

namespace KhaledSadek\BladeBoringAvatars;

class Helper
{
    public static function getNumber(string $string): int
    {
        $string = str_split($string);
        $charactersCodesSum = 0;
        foreach ($string as $char) {
            $char = mb_substr($char, 0);
            [, $ret] = unpack('S', mb_convert_encoding($char, 'UTF-16LE'));
            $charactersCodesSum += $ret;
        }

        return $charactersCodesSum;
    }

    /**
     * @param  string[]  $array
     */
    public static function getRandomElement(int $number, array $array): string
    {
        return $array[$number % count($array)];
    }

    public static function getUnit(int $number, int $range, int $index = 0): int
    {
        $value = $number % $range;
        if ($index > 0 && self::getDigit($number, $index) % 2 === 0) {
            return -$value;
        }

        return $value;
    }

    public static function getDigit(int $number, int $ntn): int
    {
        return (int) floor(($number / pow(10, $ntn)) % 10);
    }

    public static function getContrast(string $color): string
    {
        $color = str_replace('#', '', $color);
        $array = str_split($color, 2);
        $r = intval($array[0], 16);
        $g = intval($array[1], 16);
        $b = intval($array[2], 16);
        $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;

        if ($yiq >= 128) {
            return 'black';
        }

        return 'white';
    }

    public static function getBoolean(int $number, int $ntn): bool
    {
        return (self::getDigit($number, $ntn) % 2) === 0;
    }
}
