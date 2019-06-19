<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/11
 * Time: 10:52
 */

namespace app\home\model;


use think\Model;

class CnCity extends Model
{
    //按省份code获取市
    function getCity($code,$page=1)
    {
        $file = ['c.id as id',
            'c.code as code',
            'c.name as name',
        ];

        $where['c.pcode'] = $code;

        $list = $this->alias('c')
            ->field($file)
            ->order('id asc')
            ->where($where)
            ->paginate(100,false,['page'=>$page])
            ->toArray();

        return $list;

    }
}