<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/11
 * Time: 10:52
 */

namespace app\home\model;


use think\Model;

class Store extends Model
{
    /*
         * 关联审核
         */
    public function goods(){
        return $this->hasMany('Goods')->field('picurl,saleprice');
    }

    function goodslist(){
        return $this->hasMany('Goods')->field('store_id,picurl,saleprice');
    }

    //获取热推店铺
    function getHotStore($num=2,$page=1){

        $file = ['s.id as id',
                's.title as title',
                's.picurl as picurl',
        ];
        $where['hot'] = 1;

        $list = $this->alias('s')
                ->field($file)
                ->where($where)
                ->order('id desc')
                ->limit($num)
                ->paginate(20,false,['page'=>$page])
                ->toArray();

        return $list;

    }
}