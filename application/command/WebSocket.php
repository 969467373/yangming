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

class WebSocket extends Command
{
    private $serv;
    private $pdo;
    //cache的存储规则
    public $rule = "websocket";

    protected function configure()
    {
        $this->setName('WebSocket')->setDescription('Websocket listener start');
    }

    protected function execute(Input $input, Output $output)
    {
        $this->serv = new \swoole_websocket_server("0.0.0.0", 9501);
        $this->serv->set([
            'worker_num' => 1,
            'dispatch_mode' => 2,
            'daemonize' => 0,
            'max_request' => 10000,
        ]);

        $this->serv->on('open',array($this, 'open') );

        $this->serv->on('message', array($this, 'onMessage'));
        $this->serv->on('Request', array($this, 'onRequest'));


        $port1 = $this->serv->listen("0.0.0.0", 9503, SWOOLE_SOCK_TCP);

        $port1->set(array(
            'worker_num' => 1,
            'daemonize' => false,
            'max_request' => 10000,
            'dispatch_mode' => 2,
            'debug_mode'=> 1
        ));

        $port1->on('Connect', array($this, 'onConnect'));

        $port1->on('Receive', array($this, 'onReceive'));

        $this->serv->on('close',array($this, 'close') );

        $this->serv->start();
    }

    public  function open($server, $req) {
        echo "connection open: {$req->fd}\n";

        $message =  ['code'=>200, 'message'=>'connection open :'.$req->fd,'connect_id'=>$req->fd];
        $server->push($req->fd, json_encode($message));
    }

    public function onConnect( $serv, $fd, $from_id ) {
        echo "Connect成功！\n";
        //$serv->send( $fd, "Hello {$fd}!" );
    }


    //显示是哪个客户端发来的数据
    public function onMessage(\swoole_websocket_server $_server, $frame)
    {
        echo "received message: {$frame->data}\n";
        if($frame->data == '@heart')
        {
            $message = ['code' => 200 ,'message'=>'续命成功'];
            $_server->push($frame->fd, json_encode($message));
        }

        /*echo "received message: {$frame->data}\n";
        $_server->push($frame->fd, "get it message \n");
        foreach($_server->connections as $fd){
            $info = $_server->connection_info($fd);
            $_server->push($fd, "get it message from ".$frame->fd."\n");
            //var_dump($info);
        }*/
    }
    //服务端接收到不同端口的数据如何处理
    public function onRequest($request, $response){
        foreach($this->serv->connections as $fd){
            echo 1;
            $info = $this->serv->connection_info($fd);
            switch($info['server_port']){
                case 9501:
                {
                    // websocket
                    echo "websocket\n";
                    if($info['websocket_status']){}
                    echo $info['websocket_status'];
                    //$response->end("");
                }

                case 9503:
                {
                    echo "TCP\n";
                    // TCP
                }
            }

            //var_dump($info);
        }
    }

    public function onReceive( \swoole_server $serv, $fd, $from_id, $data ) {
        /**Tcp接口入口**/
        //$res = $this->parse_http($data);
        //$get = json_encode($res['get']);
        echo "Get Message From Client {$fd},data:{$data}\n";
        foreach($this->serv->connections as $fd1){
            if($fd1 != $fd)
            {
                $serv->push($fd1,$data);
            }
            //var_dump($info);
        }
        //	include_once(__DIR__.'autoload.php');
    }

    public function  close($server, $fd) {
        echo "connection close: {$fd}\n";
    }
}