<?php
namespace app\command;

use app\common\model\AuctionItem;
use app\common\model\Bid;
use app\common\model\Config;
use app\common\tool\LogTool;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\Cache;
use app\common\model\User;
use app\common\tool\JsonTool;
use think\Exception;

class Socket extends Command
{
    public $server;
    //cache的存储规则
    public $rule = "websocket";

    protected function configure()
    {
        $this->setName('Socket')->setDescription('Websocket start');
    }


    protected function execute(Input $input, Output $output)
    {
        $this->server = new \swoole_websocket_server("0.0.0.0", 10011);
        /*
        $db = new \swoole_mysql;
        $server_db = array(
            'host' => config('hostname'),
            'port' => 3306,
            'user' => config('database'),
            'password' => config('password'),
            'database' => config('database'),
            'charset' => 'utf8', //指定字符集
            'timeout' => 2,  // 可选：连接超时时间（非查询超时时间），默认为SW_MYSQL_CONNECT_TIMEOUT（1.0）
        );*/

        //swoole初始化设置
        $this->server->set([
            'reactor_num' => 2, //reactor thread num
            'worker_num' => 2,    //worker process num
            'backlog' => 128,   //listen backlog
            'max_request' => 2000,
            'dispatch_mode' => 0,
            'heartbeat_check_interval ' => 120,
        ]);

        $this->server->on('open', function (\swoole_websocket_server $server, $request){
            $from_id = $request->fd;
            echo 'connect_id:'.$from_id.' come in';
            echo "---------------------------------";
            dump($server->getClientInfo($from_id));
        });

        $this->server->on('message', function (\swoole_websocket_server $server, $frame) {

        });

        $this->server->on('close', function ($server, $fd) {
            echo "client {$fd} closed\n";
        });



        $this->server->start();
        //swoole的连接事件
        $this->server->on('open', function (\swoole_websocket_server $server, $request){
            try{
                $from_id = $request->fd;
                //检查入参(与登录同步)
                if(!empty($request->get['mac']))
                {
                    self::cache_check($request->get['mac'],$from_id);
                }else
                {
                    $error = ['code'=>400,'message'=>'mac必填'];
                    $this->server->push($request->fd, json_encode($error));
                    $server->close($request->fd);
                }
            }catch (Exception $e){
                LogTool::writeLog(APP_PATH.'log'.DS.'socket.txt','############# error:'.$e->getMessage());
            }

        });

        //swoole 获取信息事件
        $this->server->on('message', function (\swoole_websocket_server $server, $frame){
            try{
                //判断数据及类型
                if(!empty($frame->data)&&JsonTool::is_json($frame->data))
                {
                    $data = json_decode($frame->data,1);

                    //验证参数
                    if(isset($data['mac'])&&isset($data['type'])&&isset($data['auction_item_id']))//
                    {
                        //单商品常链
                        if($data['type'] == 1)
                        {
                            $model = new Bid();
                            $bid_data = $model->getBidOnTime($data['auction_item_id'],4); //获取单商品最新信息
                            $message = ['code'=>200,'message'=>'单商品常链已建立','type'=>1,'time'=>strtotime('now'),'data'=>$bid_data];
                            $this->server->push($frame->fd, json_encode($message));

                            $time = date('Y-m-d H:i:s');
                            Cache::set("timer_{$frame->fd}",$time);

                            //5秒一推送
                            $server->tick(2000, function ($id) use($frame,$data,$model){
                                $time = Cache::get("timer_{$frame->fd}");
                                $bid_data = $model->getBidContinue($data['auction_item_id'],$time);

                                if(!empty($bid_data))
                                {
                                    //刷新最新常链时间
                                    $time = date('Y-m-d H:i:s');
                                    Cache::set("timer_{$frame->fd}",$time);
                                    $message = ['code'=>200,'message'=>'单商品常链更新','type'=>1,'time'=>strtotime('now'),'data'=>$bid_data];
                                    $this->server->push($frame->fd, json_encode($message));
                                }
                            });
                        }
                        //多商品常链
                        else if($data['type'] == 2)
                        {
                            $model = new Bid();
                            $bid_data = $model->getBidListOnTime($data['auction_item_id']);//获取多商品最新信息
                            $message = ['code'=>200,'message'=>'多商品常链建立','type'=>2,'time'=>strtotime('now'),'data'=>$bid_data];

                            $this->server->push($frame->fd, json_encode($message));

                            $time = date('Y-m-d H:i:s');
                            Cache::set("timer_{$frame->fd}",$time);

                            //5秒一推送
                            $server->tick(2000, function ($id) use($frame,$data,$model){
                                $time = Cache::get("timer_{$frame->fd}");

                                $bid_data = $model->getBidListContinue($data['auction_item_id'],$time);

                                if(!empty($bid_data))
                                {
                                    $time = date('Y-m-d H:i:s');
                                    Cache::set("timer_{$frame->fd}",$time);
                                    $message = ['code'=>200,'message'=>'多商品常链更新','type'=>2,'time'=>strtotime('now'),'data'=>$bid_data];
                                    $this->server->push($frame->fd, json_encode($message));
                                }
                            });
                        }else
                        {
                            $error = ['code'=>400,'message'=>'参数错误'];
                            $this->server->push($frame->fd,json_encode($error));
                        }
                    }else
                    {
                        $error = ['code'=>400,'message'=>'缺少参数'];
                        $this->server->push($frame->fd,json_encode($error));
                    }
                }else
                {
                    if($frame->data != '@heart')
                    {
                        $error = ['code'=>400,'message'=>'参数错误'];
                        $this->server->push($frame->fd,json_encode($error));
                    }else
                    {
                        $this->server->push($frame->fd,'心跳测试');
                    }

                }

            }catch (Exception $e){
                LogTool::writeLog(APP_PATH.'log'.DS.'socket.txt','############# error:'.$e->getMessage());
            }
        });

        $this->server->on('close', function ($ser, $fd) {
            echo "client {$fd} closed\n";
        });

        $this->server->start();
    }

    //设置常链id缓存
    public function cache_check($mac,$from_id)
    {
        if(Cache::has($this->rule.'_'.$mac))
        {
            if(Cache::get($this->rule.'_'.$mac) != $from_id)
            {
                Cache::set($this->rule.'_'.$mac,$from_id);
                echo 'connect_id:'.$mac.'have change';
            }else
            {
                echo 'connect_id:'.$mac.'has coming back';
            }

        }else
        {
            Cache::set($this->rule.'_'.$mac,$from_id);
            echo 'connect_id:'.$mac.' have come in';
        }
        $message = ['code'=>200,'message'=>'连接成功','data'=>['connect_id'=>$from_id,'mac'=>$mac]];
        $this->server->push($from_id,json_encode($message));
        LogTool::writeLog(APP_PATH.'log'.DS.'socket.txt','############# connect_id:'.$from_id.' mac:'.$mac.'\n');
    }
}
/*
    //初始化
    public function onOpen(\swoole_websocket_server $server, $request)
    {
        $from_id = $request->fd;
        if(!empty($request->get['id']))
        {
            $user_id = $request->get['id'];

            $user = new User();
            $info = $user->getUserInfo($user_id);

            if(!empty($info))
            {
                self::cache_check($user_id,$from_id);
            }
        }else
        {
            $server->close($request->fd);
        }
    }

    // 收到数据时回调函数
    public function onMessage(\swoole_websocket_server $server, $frame)
    {
        echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
        $server->push($frame->fd, "this is server");
    }

    // 接收http请求从get获取message参数的值，给用户推送
    public function onRequest($request, $response)
    {
        foreach ($this->server->connections as $fd) {
            $this->server->push($fd, $request->get['message']);
        }
    }

    // 连接关闭时回调函数
    public function onClose($server, $fd)
    {
        echo "client {$fd} closed\n";
    }*/
