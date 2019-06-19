<?php
namespace app\command;

use app\common\model\Config;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;


class Test extends Command
{
    protected function configure()
    {
        $this->setName('test')->setDescription('Here is the remark ');
    }


    protected function execute(Input $input, Output $output)
    {
        $n=0;
        while (true){
            try{
                $n++;
                dump($n);
                $config = new Config();
                $auction_time = $config->getConfigValues(['name'=>['in',['auction_begin','auction_end']]]);
                $begin = strtotime($auction_time['auction_begin']);
                $end = strtotime($auction_time['auction_end']);
                if (time()<$begin||time()>$end){
                    sleep(5);
                    continue;
                }
                dump('循环');
                if ($n>50)
                    throw new \Exception('超过50次');
            }catch (\Exception $e){
                dump($e->getMessage());
                break;
            }
        }
    }
}