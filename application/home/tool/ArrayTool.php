<?php

namespace app\common\tool;


class ArrayTool
{

    //随机从数组中一个元素
    static function arrayRandGetOne($data){
        shuffle($data);
        return $data[0];
    }
    /**
     * 随机从数组中获取指定数量元素
     * @param $data,数组
     * @param $num,指定数量
     * @return array,返回数组
     */
    static function arrayRandGet($data,$num){
        shuffle($data);
        return array_slice($data, 0, $num);
    }

    /**
     * 获取两个数组间交集的数量
     * @param $array1
     * @param $array2
     * @return int
     */
    static function countArrayIntersect($array1, $array2){
        return count(array_intersect($array1, $array2));
    }

    /**
     * 计算数组的奇数数量
     * @param $data
     * @return int
     */
    static function countOdd($data){
        $odd = 0;
        foreach ($data as $v) {
            $odd += $v % 2 == 1 ? 1 : 0;
        }
        return $odd;
    }

    /**
     * 计算数组的偶数数量
     * @param $data
     * @return int
     */
    static function countEven($data){
        $even = 0;
        foreach ($data as $v) {
            $even += $v % 2 == 1 ? 0 : 1;
        }
        return $even;
    }

    /**
     * 计算数组的小数数量
     * @param $data
     * @param $median,中间数
     * @return int
     */
    static function countLow($data,$median){
        $low = 0;
        foreach ($data as $v) {
            $low += $v <= $median ? 1 : 0;
        }
        return $low;
    }

    /**
     * 计算数组的大数数量
     * @param $data
     * @param $median,中间数
     * @return int
     */
    static function countUpper($data,$median){
        $upper = 0;
        foreach ($data as $v) {
            $upper += $v <= $median ? 0 : 1;
        }
        return $upper;
    }

    /**
     * 获取目标数组和样本数组间的差集
     * @param $choose,目标数组
     * @param $base,样本数组
     * @return array
     */
    static function getDifferentArray($choose,$base){
        return array_diff($choose,$base);
    }
}