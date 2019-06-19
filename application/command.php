<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

return [
    'app\command\MakeConsole',#生成后台模块表
    'app\command\Test',#测试
    'app\command\DoAuction',#拍卖程序
    'app\command\OneAuction',#拍卖程序
    'app\command\Did',#竞拍出价
    'app\command\Socket',#websocket1
    'app\command\WebSocket',#websocket2 listener
    'app\command\CheckTimeOut',#计算超期未下单
];
