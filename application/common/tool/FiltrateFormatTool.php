<?php

namespace app\common\tool;

/**
 * 筛选项整理工具
 * Class FiltrateFormatTool
 * @package app\common\tool
 */
class FiltrateFormatTool
{
    static function timeScope($where=[],$field,$param,$begin,$end){
        $param[$begin] = empty($param[$begin])?'':Time::formatToTimestamp($param[$begin]);
        $param[$end] = empty($param[$end])?'':Time::formatToTimestamp($param[$end]);
        //如果只输入了开始时间 查询条件是 大于开始时间
        if (!empty($param[$begin]) && empty($param[$end])) {
            $where[$field] = ['>=', $param[$begin]];
        }
        //如果只输入了结束时间 查询条件是 小于结束时间
        if (empty($param[$begin]) && !empty($param[$end])) {
            $where[$field] = ['<', $param[$end]+86400];
        }
        //都输入了在这个范围内
        if (!empty($param[$begin]) && !empty($param[$end])) {
            $where[$field] = ['between', [$param[$begin],$param[$end]+86400]];
        }

        return $where;
    }

}