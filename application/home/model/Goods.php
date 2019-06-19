<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/11
 * Time: 10:52
 */

namespace app\home\model;


use think\Model;

class Goods extends Model
{
    function getHotGoods($num=4 , $page=1)
    {
        $file = ['g.id as id',
            'g.picurl as title',
            'g.title as picurl',
            'g.saleprice as saleprice',
            'g.sell as sell',
        ];
        $where['hot'] = 1;

        $list = $this->alias('g')
            ->field($file)
            ->where($where)
            ->order('id desc')
            ->limit($num)
            ->paginate(20,false,['page'=>$page])
            ->toArray();

        return $list;

    }
}