<?php

namespace App\Helper;

use Carbon\Carbon;
class TimezoneHelper{

    // static function getUtcDateTimeFromMixedData2($localDate, $utcTime, $timezone) {
    //     $timeArr = explode(':', $utcTime);
    //     $utcDateTime = Carbon::createFromTime($timeArr[0], $timeArr[1]);
    //     print_r($utcDateTime->toDateTimeString() . "\n");

    //     $localDateTime = $utcDateTime->copy()->setTimezone($timezone);
    //     print_r($localDateTime->toDateTimeString(). "\n");
    //     $dayDiff = $utcDateTime->startOfDay()->diffInDays($localDateTime->startOfDay()->shiftTimezone('UTC'));
    //     print_r("day diff" . $dayDiff);
    //     $res =  Carbon::createFromFormat('Y-m-d H:i:s',  $localDate . ' ' . $utcTime)->addDays($dayDiff);
    //     print_r("\nConvert to local" . $res->copy()->tz($timezone)->toDateTimeString());
    //     return $res;
    // }

    static function getUtcDateTimeFromMixedData($localDate, $utcTime, $timezone) {
        $timeArr = explode(':', $utcTime);
        $utcDateTime = Carbon::createFromTime($timeArr[0], $timeArr[1]);
        $localDateTime = $utcDateTime->copy()->setTimezone($timezone);
        $localDateTime = Carbon::createFromFormat('Y-m-d H:i:s',  $localDate .' '. $localDateTime->format('H:i:s'), $timezone);
        return $localDateTime->setTimezone('UTC');
    }
}
