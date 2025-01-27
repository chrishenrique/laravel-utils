<?php

declare(strict_types=1);
namespace ChrisHenrique\LaravelUtils;


use Str;
use Arr;
use Carbon\Carbon;

/**
 * Class Utils
 *
 * @package  ChrisHenrique\LaravelUtils
 * @author   Christiano Costa <chrishenrique16@hotmail.com>
 */
class Utils
{
    /**
     * Month list
     * @return array
     */
    public static function months(): array
    {
        return [
            1 => 'Janeiro',
            2 => 'Fevereiro',
            3 => 'Março',
            4 => 'Abril',
            5 => 'Maio',
            6 => 'Junho',
            7 => 'Julho',
            8 => 'Agosto',
            9 => 'Setembro',
            10 => 'Outubro',
            11 => 'Novembro',
            12 => 'Dezembro',
        ];
    }

    /**
     * Get a month
     * @return string
     */
    public static function getMonth($month): string
    {
        return Arr::get(self::months(), $month, '');
    }


    public static function dateConflicts(Carbon $start1, Carbon $end1, Carbon $start2, Carbon $end2): bool
    {
        if($start1->equalTo($start2) || $end1->equalTo($end2)) // has conflict
            return true;

        if($end2->greaterThanOrEqualTo($start1) && $end2->lessThanOrEqualTo($end1)) // has conflict
            return true;

        if($start2->greaterThanOrEqualTo($start1) && $start2->lessThanOrEqualTo($end1)) // has conflict
            return true;

        if($start2->lessThanOrEqualTo($start1) && $end2->greaterThanOrEqualTo($end1)) // has conflict
            return true;
            
        if($start2->greaterThanOrEqualTo($start1) && $end2->lessThanOrEqualTo($end1)) // has conflict
            return true;

        return false;
    }

    public static function numberConflicts($start1, $end1, $start2, $end2): bool
    {
        if($start1 === $start2 || $end1 === $end2) // has conflict
            return true;

        if(($end2 >= $start1) && ($end2 <= $end1)) // has conflict
            return true;

        if(($start2 >= $start1) && ($start2 <= $end1)) // has conflict
            return true;

        if(($start2 <= $start1) && ($end2 >= $end1)) // has conflict
            return true;
            
        if(($start2 >= $start1) && ($end2 <= $end1)) // has conflict
            return true;

        return false;
    }
    
    public static function xml2Arr(string $xml): array
    {
        try 
        {
            $xmlObj = simplexml_load_string($xml);
            return self::object2Array($xmlObj);
        } 
        catch (\Exception $e) 
        {
            return [];
        }
    }

    public static function object2Array($obj): array
    {
        return json_decode(json_encode($obj), TRUE);
    }

    public static function isJSON(string $string): bool
    {
        return is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

    // https://developer.mozilla.org/en-US/docs/Web/HTTP/MIME_types/Common_types
    public static function mimeType(string $extension):string
    {
        return Arr::get([
            'csv' => 'text/csv',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'mp3' => 'audio/mpeg',
            'mp4' => 'video/mp4',
            'mpeg' => 'video/mpeg',
            'png' => 'image/png',
            'pdf' => 'application/pdf',
            'txt' => 'text/plain',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xls' => 'application/vnd.ms-excel',
        ], $extension, '');
    }

    public static function isWeekDay(Carbon $date): bool
    {
        $weekMap = [
            // 'SU' => 0,
            'MO' => 1,
            'TU' => 2,
            'WE' => 3,
            'TH' => 4,
            'FR' => 5,
            // 'SA' => 6,
        ];

        return in_array($date->dayOfWeek, $weekMap);
    }

    /**
     * Binding query with values to show
     * @param $query
     * @return string
     */
    public static function toSql($query): string
    {
        return vsprintf(str_replace('?', '%s', $query->toSql()), collect($query->getBindings())->map(function ($binding) {
            return is_numeric($binding) ? $binding : "'{$binding}'";
        })->toArray());
    }
}