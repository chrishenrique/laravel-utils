<?php

declare(strict_types=1);
namespace ChrisHenrique\LaravelUtils;

use Str;

/**
 * Class Number
 * Perform string
 *
 * @package  ChrisHenrique\LaravelUtils
 * @author   Christiano Costa <chrishenrique16@hotmail.com>
 */
class Number {

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
     * Set the decimal precision configuration.
     * @return mixed
     */
    public static function setPrecision($precision)
    {
        self::$precision = $precision;

        return new static();
    }

    /**
     * Set the decimal precision configuration.
     * @return mixed
     */
    public static function setFormat($format)
    {
        self::$format = $format;

        return new static();
    }

    /**
     * Return the thousands separator based on decimal format application.
     * @return mixed
     */
    public static function thousands()
    {
        return static::$thousands[self::$format];
    }

    /**
     * Return the decimal separator based on decimal format application.
     * @return mixed
     */
    public static function decimal()
    {
        return static::$decimal[self::$format];
    }

    /**
     * Return the format configuration.
     * @return mixed
     */
    public static function format()
    {
        return static::$format;
    }

    /**
     * Return the decimal precision configuration.
     * @return mixed
     */
    public static function precision()
    {
        return static::$precision;
    }

    /**
     * Return the money precision configuration.
     * @return mixed
     */
    public static function moneyPrecision()
    {
        return static::$precision;
    }

    /**
     * Return the decimal separator based on decimal format application.
     * @return mixed
     */
    public static function currency()
    {
        return static::$currency[self::$format];
    }

    /**
     * Format a currency value to application configured format.
     * @param  mixed $value
     * @return mixed
     */
    public static function toIntegerApp($value)
    {
        return self::toDecimalApp($value, 0);
    }

    /**
     * Return a number from a decimal value in application configured format.
     * @param  mixed $value
     * @return mixed
     */
    public static function fromIntegerApp($value)
    {
        return self::fromDecimalApp($value, 0);
    }

	/**
     * Format a currency value to application configured format.
     * @param  mixed $value
     * @return mixed
     */
    public static function toMoneyApp($value, $precision = null, $format = 'br')
    {
        $number = self::toDecimalApp($value, $precision);

        return $number ? static::$currency[$format].' '.$number : '';
    }

    /**
     * Return a number from a currency value in application configured format.
     * @param  mixed $value
     * @return mixed
     */
    public static function fromMoneyApp($value, $precision = null, $format = 'br')
    {
        $number = self::fromDecimalApp($value, $precision);

        return $number ? static::$currency[$format].' '.$number : '';
    }

    /**
     * Para decimal da aplicação
     * Format a decimal value to application configured format.
     * @param  mixed $value
     * @return mixed
     */
    public static function toDecimalApp($value, $precision = null)
    {
        if(is_null($value) || ($value === 'null')) return '';

        $precision = $precision ?? static::precision();
        $value = str_replace('.', '', $value);
        $parts = explode(',', $value);
        $decimal = count($parts) > 1? $parts[1] : '';
        $value = number_format(self::toFormatApp($value), $precision, '.', '');
        $value = str_pad($value, $precision - strlen($decimal), '0');

        return $value;
    }

    /**
     * De decimal da aplicação
     * Return a number from a decimal value in application configured format.
     * @param  mixed $value
     * @return mixed
     */
    public static function fromDecimalApp($value, $precision = null, $thousands = '.')
    {
        if(is_null($value) || ($value === 'null')) return '';

        $precision = $precision ?? static::precision();
        $parts = explode('.', $value);
        $decimal = count($parts) > 1? $parts[1] : '';
        $value = number_format(self::toFormatApp($value), $precision, ',', $thousands);

        return str_pad($value, $precision - strlen($decimal), '0');
    }

    /**
     * Return a decimal number in raw format.
     * @param  mixed $value
     * @return mixed
     */
    public static function toDecimalRaw($value, $precision = null)
    {
        $precision = $precision ?? static::precision();
        $parts = explode('.', $value);
        $decimal = count($parts) > 1? $parts[1] : '';
        $value = number_format(self::toFormatApp($value), $precision, '.', '');

        return str_pad($value, $precision - strlen($decimal), '0');
    }

    /**
     * Return a number with a percentage discount applied.
     * @param  mixed $value
     * @param  number $percentage
     * @return mixed
     */
    public static function applyDiscount($value, $percentage = 0)
    {
        return bcsub($value, bcmul($value, bcdiv($percentage, 100)));
    }

    /**
     * Return a number with a percentage rate applied.
     * @param  mixed $value
     * @param  number $percentage
     * @return mixed
     */
    public static function applyRate($value, $percentage = 0)
    {
        return bcadd($value, bcmul($value, bcdiv($percentage, 100)));
    }

    /**
     * Return a percentage from two values.
     * @param  number $total
     * @param  number $part
     * @return number
     */
    public static function perc($total, $part)
    {
        $total = floatval($total);

        return floatval($total? bcmul(bcdiv($part, $total, 8), 100, static::precision()) : 0);
    }

    /**
     * Return a percentage from a value
     * @param  number $value
     * @param  number $percentage
     * @return number
     */
    public static function percentage($value, $percentage)
    {
        return floatval($value? bcdiv(bcmul($percentage, $value, 3), 100, 3) : 0);
    }

    /**
     * Return a proportion from two values.
     * @param  number $total
     * @param  number $part
     * @return number
     */
    public static function prop($total, $part)
    {
        $total2 = floatval($total);

        return floatval($total2? bcdiv(bcmul($part, 100, 8), $total, static::precision()) : 0);
    }

    /**
     * Return a value to total by percent.
     * @param  number $total
     * @param  number $part
     * @return number
     */
    public static function valueToPerc($total, $percentage)
    {
        // $total = floatval($total);

        return floatval(bcmul(bcdiv($percentage, 100, 3), $total, static::precision()));
    }

    /**
     * Check if the value is money app format
     * @param  mixed $value
     * @return bool
     */
    public static function isMoneyApp($value)
    {
        return static::isDecimalApp($value);
    }

    /**
     * Check if the value is app format
     * @param  mixed $value
     * @param  string $format
     * @return bool
     */
    public static function isDecimalApp($value)
    {
        $precision = static::precision();
        $key = $precision > 0? 1 : 0;
        $fmt = static::format();

        $patterns = [
            'int' => ['/^\d{1,3}(,\d{3})$/', '/^\d{1,3}(,\d{3})*\.\d{:p}$/'],
            'br'  => ['/^\d{1,3}(\.\d{3})$/', '/^\d{1,3}(\.\d{3})*,\d{:p}$/']
        ];

        $pattern = str_replace(':p', $precision, $patterns[$fmt][$key]);

        return (bool)preg_match($pattern, (string)$value);
    }

    public static function truncate($number, $precision = null, $decimal = '.')
    {
        $number = str_replace(',', '.', $number);
        $precision = $precision ?? static::precision();
        return substr($number, 0, (strpos($number, $decimal) ?: strlen($number)) + $precision + ($precision == 0 ? 0 : 1));
    }

    /**
     * Format value to international format
     * @param mixed $value
     * @return mixed
     */
    public static function toFormatApp($value)
    {
        $value = (string)$value;
        $value = str_replace('.', ',', $value);
        $value = Str::replaceLast(',', '.', $value);
        $value = str_replace(',', '', $value);
        $value = floatval($value);

        return $value;

    }

}
