<?php
namespace app\home\controller;

use app\home\model\User as usermodel;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;
use traits\controller\Jump;

class User extends Controller
{//我的(消息,资料,设置,认证,积分,开店,客服中心,意见反馈,分享)

    //密码加密
    public function setPasswordAttr($value)
    {
        //密码哈希加密
        return password_hash($value, PASSWORD_DEFAULT);
    }





}
