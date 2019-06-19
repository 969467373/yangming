<?php
namespace app\common\tool;

class ClientTool
{
    public $cli;
    private $ip;
    private $port;
    public function __construct($ip, $port)
    {
        $this->ip = $ip;//请求地址
        $this->port = $port;//端口号
        $this->cli = new \swoole_client(SWOOLE_SOCK_TCP);
        $this->cli->connect($ip,$port);
    }

    public function send($data)
    {
        $this->cli->send($data);
    }

}
