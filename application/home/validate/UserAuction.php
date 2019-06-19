<?php

namespace app\mobile\validate;

use app\common\model\AuctionItem;
use app\common\model\User;
use think\Validate;

class UserAuction extends Validate
{
    protected $rule = [
        'user_id' => 'require|gt:0|integer',
        'page' => 'gt:0|integer',
    ];
    protected $message = [
        'user_id.require' => '用户id不可缺少',
        'user_id.gt' => '用户id异常',
        'user_id.integer' => '用户id异常',

        'page.gt' => '分页页码必须大于0',
        'page.integer' => '分页页码必须是数字',
    ];

    protected $scene = [
        'getUserAuctionList'=>['user_id','page'],
        'getUserAuctioningList'=>['user_id','page'],
        'getUserAuctionedList'=>['user_id','page'],
        'getUserMissAuctionList'=>['user_id','page'],
        'getToBuyAuctionList'=>['user_id','page'],
    ];

    // 验证用户余额,不足返回420
    protected function checkCurrency($value, $rule, $data)
    {
        $user_model = new User();
        $AuctionItem = new AuctionItem();
        $cost = $AuctionItem->where('id',$data['auction_item_id'])->value('cost');
        $user_currency = $user_model->where('id',$value)->value($data['currency_type']);

        if ($user_currency<$cost)
            abort(json(['code'=>420,'message'=>'余额不足']));
        return true;
    }
}
