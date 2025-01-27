<?php

declare(strict_types=1);
namespace ChrisHenrique\LaravelUtils;

use Illuminate\Support\Arr as ArrBase;

/**
 * Class Arr
 * Perform arrays
 *
 * @package  ChrisHenrique\LaravelUtils
 * @author   Christiano Costa <chrishenrique16@hotmail.com>
 */
class Arr extends ArrBase
{
    /**
     * Get and check array value is valid
     *
     * @param array $array
     * @param mixed $key
     * @return mixed | null
     */
    public static function val($array, $key, $callback = null)
    {
        if(is_null($key)) return $key;
        if (!($value = Base::get($array, $key, $callback)))
        {
            return $callback;
        }

        return $value;
    }

    public static function implodeKeys($glue, $array, $separator = ' = ', $delimited = ','): string
    {
        $str = '';
        foreach($array as $key=>$item) {
            $str .=$key.$separator.$item.$delimited.$glue;
        }
        return rtrim($str, ',');
    }

    public static function implode(array $array, $separator = ', ', $arrKey = null): string
    {
        $newArr = [];

        foreach ($array as $key => $value)
        {
            if(!$value) unset($array[$key]);

            if(is_array($value) && $arrKey)
            {
                $newArr[] = Arr::get($value, $arrKey);
            }
        }

        return implode($separator, empty($newArr) ? $array : $newArr);
    }

    public static function explode($str, $separator = ', '): array
    {
        return explode($separator, $str);
    }

    public static function changeKey( $array, $oldKey, $newKey ): array
    {

        if( ! array_key_exists( $oldKey, $array ) )
            return $array;

        $keys = array_keys( $array );
        $keys[ array_search( $oldKey, $keys ) ] = $newKey;

        return array_combine( $keys, $array );
    }

    /**
     * Conditionally compile classes from an array into a CSS class list.
     *
     * @param  array  $array
     * @return string
     */
    public static function toCssClasses($array)
    {
        $classList = static::wrap($array);

        $classes = [];

        foreach ($classList as $class => $constraint) {
            if (is_numeric($class)) {
                $classes[] = $constraint;
            } elseif ($constraint) {
                $classes[] = $class;
            }
        }

        return implode(' ', $classes);
    }


    public static function get($array, $key, $default = null)
    {
        if (! static::accessible($array)) {
            return value($default);
        }

        foreach (explode('|', $key) as $segment) 
        {
            if (static::accessible($array) && static::exists($array, $segment)) {
                return Base::get($array, $segment, $default);
            }
        }

        return Base::get($array, $key, $default);
    }

    public static function toXml(array $array, $xmlObj = null)
    {
        $xml = $xmlObj ?? new \SimpleXMLElement('<data/>');
        foreach ($array as $k => $v) {
            is_array($v)
                ? self::toXml($v, $xml->addChild($k))
                : $xml->addChild($k, $v);
        }

        return $xml->asXML();
    }

    public static function sanitize($array)
    {
        if(!is_array($array))
            return $array;

        if(empty($array))
            return null;

        $items = array_map(function($item) {
            return is_array($item) ? self::sanitize($item) : $item;
        }, $array);

        return array_filter($items, function($item){
            return $item !== null && (!is_array($item) || count($item) > 0);
        });
    }
}
