<?php

declare(strict_types=1);
namespace ChrisHenrique\LaravelUtils;

use Illuminate\Support\Str as StrBase;
use Carbon\Carbon;

/**
 * Class Str
 * Perform string
 *
 * @package  ChrisHenrique\LaravelUtils
 * @author   Christiano Costa <chrishenrique16@hotmail.com>
 */
class Str extends StrBase
{
    public static function shuffle($string)
    {
        $stringParts = str_split($string);
        shuffle($stringParts);
        return implode($stringParts);
    }

    public static function daysToDate($days, $format = 'd/m/Y')
    {
        if(!$days) return $days;

        $result = Carbon::createFromDate(1900, 01, 01)->addDays($days)->subDays(2);

        return $format ? $result->format($format) : $result;
    }

    public static function slugfy($text, $separator = '-')
    {
        if (is_string($text))
        {
            $text = strtolower(trim(utf8_decode($text)));

            $before = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr';
            $after  = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
            $text = strtr($text, utf8_decode($before), $after);

            $replace = array(
                    '/[^A-Za-z0-9.-@]/'	=> $separator,
                    '/-+/'			=> $separator,
                    '/\-{2,}/'		=> ''
            );
            $text = preg_replace(array_keys($replace), array_values($replace), $text);
        }

        return $text;
    }

    public static function minify($text)
    {
        if (is_string($text))
        {
            $text = strtolower(trim(utf8_decode($text)));

            $before = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr';
            $after  = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
            $text = strtr($text, utf8_decode($before), $after);

            $replace = array(
                    '/[^a-z0-9.-@]/'	=> '',
                    '/-+/'			=> '',
                    '/\-{2,}/'		=> ''
            );
            $text = preg_replace(array_keys($replace), array_values($replace), $text);
        }

        return $text;
    }

    public static function removeAccents($text)
    {
        if (is_string($text))
        {
            $text = strtolower(trim(utf8_decode($text)));

            $before = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr';
            $after  = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
            $text = strtr($text, utf8_decode($before), $after);
        }

        return $text;
    }

    public static function querify($params, $separator = '&')
    {
        $query = '';
        if (is_array($params))
        {
            $query = http_build_query($params, '', $separator);
        }

        return $query;
    }

    public static function bind($text, array $search = [])
    {
        if (is_string($text))
        {
            foreach ($search as $key => $value)
            {
                $text = str_replace(":{$key}", $value, $text);
            }
        }

        return $text;
    }

    public static function basicPassword(): string
    {
        return self::random(8);
    }

    public static function basicMask(string $str, $mask): string
    {
        /*
           ($cnpj, '##.###.###/####-##')
           ($cpf, '###.###.###-##')
           ($cep, '#####-###')
           ($data, '##/##/####')
           ($data, '[##][##][####]')
           ($data, '(##)(##)(####)')
           ($hora, '## horas ## minutos e ## segundos')
           ($hora, '##:##:##')
         */
        switch ($mask) {
            case 'phone':
                $mask = '(##) #####-####';
                break;
            case 'cnpj':
                $mask = '##.###.###/####-##';
                break;
            case 'cnpj_simple':
                $mask = '########/####-##';
                break;
            case 'cpf':
                $mask = '###.###.###-##';
                break;
            case 'cpf_simple':
                $mask = '#########-##';
                break;
            case 'cep':
                $mask = '#####-###';
                break;
            case 'data':
                $mask = '##/##/####';
                break;
            case 'time':
                $mask = '##:##';
                break;
            case 'datatime':
                $mask = '##/##/#### ##:##';
                break;
            case 'timeHuman':
                $mask = '## hora(s) ## minuto(s) e ## segundo(s)';
                break;
            default:
                break;
        }

        if(!$str)
            return '';

        $maskared = '';
        $k = 0;
        for ($i = 0; $i <= strlen($mask) - 1; ++$i) {
            if ($mask[$i] == '#') {
                if (isset($str[$k])) {
                    $maskared .= $str[$k++];
                }
            } else {
                if (isset($mask[$i])) {
                    $maskared .= $mask[$i];
                }
            }
        }

        return $maskared;
    }

    public static function size($size, array $options=null)
    {
        $o = [
            'binary' => false,
            'decimalPlaces' => 2,
            'decimalSeparator' => ',',
            'thausandsSeparator' => '',
            'maxThreshold' => false, // or thresholds key
            'sufix' => [
                'thresholds' => ['', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y'],
                'decimal' => ' {threshold}B',
                'binary' => ' {threshold}iB'
            ]
        ];

        if ($options !== null)
            $o = array_replace_recursive($o, $options);

        $count = count($o['sufix']['thresholds']);
        $pow = $o['binary'] ? 1024 : 1000;

        for ($i = 0; $i < $count; $i++)

            if (($size < pow($pow, $i + 1)) ||
                ($i === $o['maxThreshold']) ||
                ($i === ($count - 1))
            )

            return number_format(
                        $size / pow($pow, $i),
                        $o['decimalPlaces'],
                        $o['decimalSeparator'],
                        $o['thausandsSeparator']
                    ).str_replace(
                        '{threshold}',
                        $o['sufix']['thresholds'][$i],
                        $o['sufix'][$o['binary'] ? 'binary' : 'decimal']
                    );
    }

    public static function utf8ToXml(string $string): string
    {
        return preg_replace ('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $string) ?? $string;
    }

    public static function pluralBySize(array $strs, int $size, string $str2SizeZero = '')
    {
        if($size == 0 && $str2SizeZero) return $str2SizeZero;

        return $size == 1 ? $strs[0] : $strs[1];
    }

    public static function quickRandom($length = 16): string
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }

    public static function withoutNumbers(string $str): string
    {
        return preg_replace('/[0-9]+/', '', $str);
    }

    public static function onlyNumbers(string $str): string
    {
        return preg_replace('/[^0-9]/', '', $str);
    }

    public static function withoutSpecialChars(string $str): string
    {
        return preg_replace('/[^A-Za-z0-9]/', '', $str);
    }
}
