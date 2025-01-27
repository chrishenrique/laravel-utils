<?php

declare(strict_types=1);
namespace ChrisHenrique\LaravelUtils;


use Str;

/**
 * Class Phone
 *
 * @package  ChrisHenrique\LaravelUtils
 * @author   Christiano Costa <chrishenrique16@hotmail.com>
 */
class Phone
{
    public static function addDDI(string $number, $code = '55'): string
    {
        return Str::padLeft($number, Str::length($number) == 10 ? 12 : 13, $code);
    }
}