<?php

namespace app\console\controller;



use app\common\controller\ConsoleBase;
use app\common\model\Adminer;
use app\common\model\TaskFlow;
use app\common\model\User;

use think\Controller;


class Dashboard extends ConsoleBase

{

    private $title = '主面板';


    public function index(){

        //管理员数量
        $admin = new Adminer();
        $info['admin'] = $admin->count('id');

        //用户数量
        $user = new User();
        $info['user'] = $user->count('id');

        //进行中
        $flow = new TaskFlow();
        $info['doing'] = $flow->where('status',1)->count('id');
        //已完成
        $info['finish'] = $flow->where('status',2)->count('id');

        $system_info = [
            '操作系统' => PHP_OS,
            '运行环境' => $_SERVER["SERVER_SOFTWARE"],
            '服务器时间' => date("Y年n月j日 H:i:s"),
            '用户的IP地址' => $_SERVER['REMOTE_ADDR'],
            '主机名' => $_SERVER['SERVER_NAME'],
            '通信协议' => $_SERVER['SERVER_PROTOCOL'],
        ];

        $system_info2 = [
            'ThinkPHP版本' => THINK_VERSION,
            '剩余空间' => round((disk_free_space(".") / (1024 * 1024)), 2) . 'M',
            '北京时间' => gmdate("Y年n月j日 H:i:s", time() + 8 * 3600),
            '服务器域名/IP' => $_SERVER['SERVER_NAME'] . ' [ ' . gethostbyname($_SERVER['SERVER_NAME']) . ' ]',
            '上传附件限制' => ini_get('upload_max_filesize'),
            '执行时间限制' => ini_get('max_execution_time') . '秒',
        ];

        return $this->fetch('index',[

            'title'=> '主面板',
            'info' => $info,
            'system_info' => $system_info,
            'system_info2' => $system_info2,

        ]);

    }

    // @# 服务器信息-服务器信息-服务器信息-serverinfo
    public function serverinfo()

    {

        $info = [

            '操作系统'       =>PHP_OS,

            '运行环境'       =>$_SERVER["SERVER_SOFTWARE"],

            '主机名'         =>$_SERVER['SERVER_NAME'],

            'WEB服务端口'    =>$_SERVER['SERVER_PORT'],

            '网站文档目录'   =>$_SERVER["DOCUMENT_ROOT"],

            '浏览器信息'     =>substr($_SERVER['HTTP_USER_AGENT'], 0, 40),

            '通信协议'       =>$_SERVER['SERVER_PROTOCOL'],

            '请求方法'       =>$_SERVER['REQUEST_METHOD'],

            'ThinkPHP版本'   =>THINK_VERSION,

            '上传附件限制'   =>ini_get('upload_max_filesize'),

            '执行时间限制'   =>ini_get('max_execution_time').'秒',

            '服务器时间'     =>date("Y年n月j日 H:i:s"),

            '北京时间'       =>gmdate("Y年n月j日 H:i:s",time()+8*3600),

            '服务器域名/IP'  =>$_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]',

            '用户的IP地址'   =>$_SERVER['REMOTE_ADDR'],

            '剩余空间'       =>round((disk_free_space(".")/(1024*1024)),2).'M',



        ];

        return $this->fetch('serverinfo',[

            'title'=> '服务器信息',

            'info'=>$info

        ]);

    }

}

