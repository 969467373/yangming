<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/11
 * Time: 10:52
 */

namespace app\home\model;


use think\Model;

class CnProv extends Model
{
    function getAllProv($page =1){
        $file = ['p.id as id',
            'p.code as code',
            'p.name as name',
        ];

        $list = $this->alias('p')
            ->field($file)
            ->order('id asc')
            ->paginate(100,false,['page'=>$page])
            ->toArray();

        return $list;

    }
}