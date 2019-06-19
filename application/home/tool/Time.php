<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2017/6/22
 * Time: 13:38
 */

namespace app\common\tool;


/**
 * 时间工具
 */
class Time
{
    /**
     * 自适应'-或/'连接的时间格式转换成时间戳
     * @param $time
     * @return false|int
     */
    static function formatToTimestamp($time){
        preg_match('/^[0-9]*$/',$time,$match);
        //如果参数本身是时间戳直接返回,非时间戳转换后返回时间戳
        return empty($match)? strtotime($time) : $time;
    }

    //返回微秒时间戳
    static function microTimeFloat()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

     static function secToTime($times){
        $result = '00:00:00';
        if ($times>0) {
            $hour = floor($times/3600);
            $minute = floor(($times-3600 * $hour)/60);
            $second = floor((($times-3600 * $hour) - 60 * $minute) % 60);
            $result = $hour.':'.$minute.':'.$second;
        }
        return $result;
    }

}