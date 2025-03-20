<?php

declare(strict_types=1);
namespace ChrisHenrique\LaravelUtils;


use Str;
use Override;
use Illuminate\Support\Number as BaseNumber;
// https://github.com/laravel/framework/blob/11.x/src/Illuminate/Support/Number.php

/**
 * Class Number
 * Perform string
 *
 * @package  ChrisHenrique\LaravelUtils
 * @author   Christiano Costa <chrishenrique16@hotmail.com>
 */
class Number extends BaseNumber
{
    /**
     * Thousands map
     * @var array
     */
    protected static $thousands = [
        'int' => ',',
        'br'  => '.',
    ];

    /**
     * Decimal map
     * @var array
     */
    protected static $decimal = [
        'int' => '.',
        'br'  => ',',
    ];

    /**
     * Currency map
     * @var array
     */
    protected static $currency = [
        'int' => '$',
        'br'  => 'R$',
    ];

    /**
     * Precision
     * @var integer
     */
    protected static $precision = 2;

    /**
     * Format
     * @var string
     */
    protected static $format = 'br';

    /**
     * Value
     * @var string|float
     */
    protected static $value = null;

    public function __construct($value = null)
    {
        static::$value = $value;
    }


    public static function setValue($value)
    {
        static::$value = $value;

        return new static();
    }


    public static function setPrecision($precision)
    {
        static::$precision = $precision;

        return new static();
    }


    public static function setFormat($format)
    {
        static::$format = $format;

        return new static();
    }


    public static function thousands()
    {
        return static::$thousands[self::$format];
    }


    public static function decimal()
    {
        return static::$decimal[self::$format];
    }

    #[Override]
    public static function currency()
    {
        return static::$currency[self::$format];
    }


    public static function toIntegerApp(): string
    {
        return self::toDecimalApp(static::$value, 0);
    }


    public static function fromIntegerApp(): string
    {
        return self::fromDecimalApp(static::$value, 0);
    }


    public static function toMoneyApp(): string
    {
        $number = self::toDecimalApp(static::$value, static::$precision);

        return $number ? static::currency().' '.$number : '';
    }


    public static function fromMoneyApp(): string
    {
        $number = self::fromDecimalApp(static::$value, static::$precision);

        return $number ? static::currency().' '.$number : '';
    }


    public static function toDecimalApp(): string
    {
        $value = static::$value;
        if(is_null($value) || ($value === 'null')) return null;

        $precision = static::$precision;
        $value = str_replace('.', '', $value);
        $parts = explode(',', $value);
        $decimal = count($parts) > 1? $parts[1] : '';
        $value = number_format(self::toFormatApp($value), $precision, '.', '');
        $value = str_pad($value, $precision - strlen($decimal), '0');

        return $value;
    }
    
    public static function fromDecimalApp(): string
    {
        $value = static::$value;
        if(is_null($value) || ($value === 'null')) return '';

        $precision = static::$precision;
        $parts = explode('.', $value);
        $decimal = count($parts) > 1? $parts[1] : '';
        $value = number_format(self::toFormatApp($value), $precision, ',', static::thousands());

        return str_pad($value, $precision - strlen($decimal), '0');
    }


    public static function toDecimalRaw(): string
    {
        $value = static::$value;
        $precision = static::$precision;
        $parts = explode('.', $value);
        $decimal = count($parts) > 1? $parts[1] : '';
        $value = number_format(self::toFormatApp($value), $precision, '.', '');

        return str_pad($value, $precision - strlen($decimal), '0');
    }


    public static function applyDiscount($percentage = 0): string
    {
        $value = static::$value;
        return bcsub($value, bcmul($value, bcdiv((string)$percentage, '100')));
    }


    public static function applyRate($percentage = 0): string
    {
        $value = static::$value;
        return bcadd($value, bcmul($value, bcdiv((string)$percentage, '100')));
    }


    public static function perc($total, $part): float
    {
        $total = floatval($total);

        return floatval($total? bcmul(bcdiv((string)$part, (string)$total, 8), '100', static::$precision) : 0);
    }

    #[\Override]
    public static function percentage($percentage): float
    {
        $value = static::$value;
        return floatval($value? bcdiv(bcmul((string)$percentage, (string)$value, 3), '100', 3) : 0);
    }


    public static function prop($total, $part): float
    {
        $total2 = floatval($total);

        return floatval($total2? bcdiv(bcmul((string)$part, '100', 8), (string)$total, static::$precision) : 0);
    }


    public static function valueToPerc($percentage): float
    {
        return floatval(bcmul(bcdiv($percentage, '100', 3), (string)static::$value, static::$precision));
    }


    public static function isMoneyApp(): bool
    {
        return static::isDecimalApp(static::$value);
    }


    public static function isDecimalApp(): bool
    {
        $precision = static::$precision;
        $key = $precision > 0? 1 : 0;
        $fmt = static::$format;

        $patterns = [
            'int' => ['/^\d{1,3}(,\d{3})$/', '/^\d{1,3}(,\d{3})*\.\d{:p}$/'],
            'br'  => ['/^\d{1,3}(\.\d{3})$/', '/^\d{1,3}(\.\d{3})*,\d{:p}$/']
        ];

        $pattern = str_replace(':p', (string)$precision, $patterns[$fmt][$key]);

        return (bool)preg_match($pattern, (string)static::$value);
    }


    public static function truncate($decimal = '.')
    {
        $number = str_replace(',', '.', static::$value);
        $precision = static::$precision;
        return substr($number, 0, (strpos($number, $decimal) ?: strlen($number)) +$precision + ($precision == 0 ? 0 : 1));
    }


    public static function toFormatApp()
    {
        $value = (string)static::$value;
        $value = str_replace('.', ',', $value);
        $value = Str::replaceLast(',', '.', $value);
        $value = str_replace(',', '', $value);
        $value = floatval($value);

        return $value;

    }

}
