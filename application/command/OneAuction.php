<?php
namespace app\command;

use app\common\model\AuctionItem;
use app\common\model\AutoBid;
use app\common\model\Bid;
use app\common\model\Config;
use app\common\model\Goods;
use app\common\model\User;
use app\common\service\Auction;
use app\common\tool\Time;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;
use think\Exception;


class OneAuction extends Command
{
    protected function configure()
    {
        //设置参数
        $this->addArgument('goods_id', Argument::REQUIRED); //必传参数
        $this->setName('OneAuction')->setDescription('指定商品开始上架拍卖');
    }

    protected function execute(Input $input, Output $output)
    {
        $goods_id = $input->getArgument('goods_id');

        while (true){
            try{
                $config = new Config();
                $auction_time = $config->getConfigValues(['name'=>['in',['auction_begin','auction_end']]]);
                $begin = strtotime($auction_time['auction_begin']);
                $end = strtotime($auction_time['auction_end']);
                if (time()<$begin||time()>$end){
                    sleep(60);
                    continue;
                }
                sleep(10);
                //上架拍卖品
                $service = new Auction();
                $add_res = $service->addAuctionItem($goods_id);
                if ($add_res)//进行拍卖
                    //$service->oneAuctioning($add_res['auction_item_id'],$add_res['min_price']);
                    throw new \Exception('上架成功');
                else
                    throw new \Exception('新拍卖品上架失败');
            }catch (\Exception $e){
                dump($e->getMessage());
                //$goods_model = new Goods();
                //$goods_model->where('id', $goods_id)->setField('status',1);
                break;
            }
        }
    }
}