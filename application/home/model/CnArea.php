<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/11
 * Time: 10:52
 */

namespace app\home\model;


use think\Model;

class CnArea extends Model
{
    //按市code获取区
    function getArea($code,$page=1)
    {
        $file = ['a.id as id',
            'a.code as code',
            'a.name as name',
        ];

        $where['a.pcode'] = $code;

        $list = $this->alias('a')
            ->field($file)
            ->order('id asc')
            ->where($where)
            ->paginate(100,false,['page'=>$page])
            ->toArray();

        return $list;

    }
}