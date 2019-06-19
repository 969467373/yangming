<?php

namespace app\common\tool;


class LogTool
{
    //记录遗漏统计错误日志
    static function writeLog($file,$log){
        $str = '日志生成时间:'.date('Y-m-d H:i:s')."\n".$log."\n";
        file_put_contents($file, $str, FILE_APPEND | LOCK_EX);
    }
}