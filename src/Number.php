<?php

declare(strict_types=1);
namespace ChrisHenrique\LaravelUtils;


use Str;
use Illuminate\Support\Number as BaseNumber;
use Stringable;

// https://github.com/laravel/framework/blob/11.x/src/Illuminate/Support/Number.php

/**
 * Class Number
 * Perform string
 *
 * @package  ChrisHenrique\LaravelUtils
 * @author   Christiano Costa <chrishenrique16@hotmail.com>
 */
class Number extends BaseNumber  implements Stringable
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
     * Symbol currency map
     * @var array
     */
    protected static $symbols = [
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
    protected $value = null;

    public function __construct($value = '')
    {
        $this->value = (string)$value;
    }

    public function __toString()
    {
        return (string)$this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
        return new static($this->value);
    }

    public function setPrecision($precision)
    {
        static::$precision = $precision;
        return new static($this->value);
    }

    public function setFormat($format)
    {
        static::$format = $format;
        return new static($this->value);
    }

    public function thousands(): string
    {
        return static::$thousands[self::$format];
    }

    public function decimal(): string
    {
        return static::$decimal[self::$format];
    }

    public static function symbol(): string
    {
        return static::$symbols[self::$format];
    }

    public function toIntegerApp()
    {
        $this->value = self::toDecimalApp($this->value, 0);
        return new static($this->value);
    }

    public function toDecimalApp(): string
    {
        $value = $this->value;
        
        if(is_null($value) || ($value === 'null')) return '';

        $precision = static::$precision;
        $value = str_replace('.', '', $value);
        $parts = explode(',', $value);
        $decimal = count($parts) > 1? $parts[1] : '';
        $value = number_format(self::toFormatApp($value), $precision, '.', '');
        $value = str_pad($value, $precision - strlen($decimal), '0');

        return $value;
    }

    public function toDecimalRaw(): string
    {
        $value = $this->value;
        $precision = static::$precision;
        $parts = explode('.', $value);
        $decimal = count($parts) > 1? $parts[1] : '';
        $value = number_format(self::toFormatApp($value), $precision, '.', '');

        return str_pad($value, $precision - strlen($decimal), '0');
    }

    public function fromIntegerApp(): string
    {
        return self::fromDecimalApp($this->value, 0);
    }

    public function fromMoneyApp(): string
    {
        $number = self::fromDecimalApp();
        return static::symbol().' '.$number;
    }
    
    public function fromDecimalApp(): string
    {
        $value = $this->value;
        if(is_null($value) || ($value === 'null')) return '';

        $precision = static::$precision;
        $parts = explode('.', $value);
        $decimal = count($parts) > 1? $parts[1] : '';
        $value = number_format(self::toFormatApp($value), $precision, ',', static::thousands());

        return str_pad($value, $precision - strlen($decimal), '0');
    }

    public function applyDiscount($percentage = 0)
    {
        $value = $this->value;
        $this->value = bcsub($value, bcmul($value, bcdiv((string)$percentage, '100')));
    }

    public function applyRate($percentage = 0)
    {
        $value = $this->value;
        $this->value = bcadd($value, bcmul($value, bcdiv((string)$percentage, '100')));
    }

    public function perc($total, $part)
    {
        $total = floatval($total);
        $this->value = floatval($total? bcmul(bcdiv((string)$part, (string)$total, 8), '100', static::$precision) : 0);
    }

    public function percent($percentage)
    {
        $value = $this->value;
        $this->value = floatval($value? bcdiv(bcmul((string)$percentage, (string)$value, 3), '100', 3) : 0);
    }

    public function prop($total, $part)
    {
        $total2 = floatval($total);
        $this->value = floatval($total2? bcdiv(bcmul((string)$part, '100', 8), (string)$total, static::$precision) : 0);
    }

    public function valueToPerc($percentage)
    {
        $this->value = floatval(bcmul(bcdiv($percentage, '100', 3), (string)$this->value, static::$precision));
    }

    public function isMoneyApp(): bool
    {
        return static::isDecimalApp($this->value);
    }

    public function isDecimalApp($number = null): bool
    {
        $precision = static::$precision;
        $key = $precision > 0? 1 : 0;
        $fmt = static::$format;

        $patterns = [
            'int' => ['/^\d{1,3}(,\d{3})$/', '/^\d{1,3}(,\d{3})*\.\d{:p}$/'],
            'br'  => ['/^\d{1,3}(\.\d{3})$/', '/^\d{1,3}(\.\d{3})*,\d{:p}$/']
        ];

        $pattern = str_replace(':p', (string)$precision, $patterns[$fmt][$key]);

        return (bool)preg_match($pattern, (string)$this->value ?? $number);
    }

    public function truncate($decimal = '.')
    {
        $number = str_replace(',', '.', $this->value);
        $precision = static::$precision;
        $this->value = substr($number, 0, (strpos($number, $decimal) ?: strlen($number)) +$precision + ($precision == 0 ? 0 : 1));
    }

    public function toFormatApp(): float
    {
        $value = (string)$this->value;
        $value = str_replace('.', ',', $value);
        $value = Str::replaceLast(',', '.', $value);
        $value = str_replace(',', '', $value);
        $value = floatval($value);

        return $value;
    }

    public function sum($value)
    {
        $this->value = bcadd($this->value, (string)$value);
        return new static($this->value);
    }

    public function sub($value)
    {
        $this->value = bcsub($this->value, (string)$value);
        return new static($this->value);
    }

}
