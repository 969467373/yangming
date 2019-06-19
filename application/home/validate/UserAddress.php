<?php

namespace app\mobile\validate;

use app\common\model\AuctionItem;
use app\common\model\User;
use think\Validate;

class UserAddress extends Validate
{
    protected $rule = [
        'user_id' => 'require|integer',
        'receiver_name' => 'require',
        'receiver_phone' => 'require',
        'area' => 'require|is_regular',
        'street' => 'require',
        'details' => 'require',
        'is_default' => "in:1,2",
        'address_id' => "require|integer|checkAddressOwner",
    ];
    protected $message = [
        'user_id.require' => '用户id必须',
        'user_id.integer' => '用户id格式错误',
        'receiver_name.require' => '收货人姓名必须',
        'receiver_phone.require' => '联系电话必须',
        'area.require' => '地区必须',
        'street.require' => '街道必须',
        'details.require' => '地址详情必须',
        'is_default.require' => '是否默认必须',

        'address_id.require' => '收货地址id必须',
        'address_id.integer' => '收货地址id格式错误',
    ];

    protected $scene = [
        'add'=>['user_id','receiver_name','receiver_phone','area','street','details','is_default'],
        'update'=>['address_id','user_id','receiver_name','receiver_phone','area','street','details','is_default'],
        'delete'=>['address_id'],
        'address'=>['address_id','user_id','is_default'],
    ];

    // 验证收货地址所有权
    protected function checkAddressOwner($value, $rule, $data)
    {
        $model = new \app\common\model\UserAddress();
        $res = $model->where(['id'=>$value,'user_id'=>$data['user_id']])->value('id');

        return $res?true:'操作地址不属于当前用户';
    }

    //地区是否包含'请选择'
    protected function is_regular($value)
    {
        //特殊字符串
        $pregs = '/请选择/';

        //字符串
        $check = preg_match($pregs, $value);

        if ($check == 1) {
            return '地区信息有误';
        } else {
            return true;
        }
    }
}
