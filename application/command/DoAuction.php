<?php
namespace app\command;

use app\common\model\AuctionItem;
use app\common\model\AutoBid;
use app\common\model\Bid;
use app\common\model\User;
use app\common\service\Auction;
use app\common\tool\Time;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Exception;


class DoAuction extends Command
{
    protected function configure()
    {
        $this->setName('DoAuction')->setDescription('进行竞拍');
    }

    protected function execute(Input $input, Output $output)
    {
        try{
            $service = new Auction();

            $begin = Time::microTimeFloat();
            $service->auctioning();
            $end = Time::microTimeFloat();
            dump('耗费:'.($end-$begin));
        }catch (Exception $e){
            dump($e->getTraceAsString());
          	echo "-------------";
          	echo 'has error:'.$e->getMessage();
            abort($this->execute());
        }

    }

//    //竞价拍卖
//    function biding($auction_item_id,$min_price){
//        $bid_service = new \app\common\service\Bid();
//        $bid_model = new Bid();
//        $auto_bid_model = new AutoBid();
//        $start = time();
//
//        //拍卖初始化剩余秒数
//        cache("{$auction_item_id}_left_time",11,3600);
//        //拍卖初始化开始时间点
//        cache("{$auction_item_id}_start",$start,86400);
//
//        while(true){
//            sleep(1);
//            $left_time = cache("{$auction_item_id}_left_time")+$start-time();
//            dump('本轮剩余'.$left_time.'秒');
//            //如果剩余时间<3秒,自动出价
//            if (cache("{$auction_item_id}_left_time")+$start-time()<3){
//                $bid_service->autoBiding($auction_item_id);
//            }
//
//            //如果剩余时间<3秒
//            //总募集资金<底价 或 该竞拍物品仍存在自动竞拍次数(为了耗光自动竞拍次数)
//            //伪造出价
//            if (
//                cache("{$auction_item_id}_left_time")+$start-time()<3 &&
//                (
//                    $bid_model->getBidTotalPrice($auction_item_id)<$min_price
//                    || $auto_bid_model->getAutoBidTimes($auction_item_id)
//                )
//            ){
//                $bid_service->falseBiding($auction_item_id);
//            }
//
//            //倒计时归零,退出拍卖循环
//            if (cache("{$auction_item_id}_left_time")+$start-time()<=0){
//                if ($this->auctionResult($auction_item_id)){
//                    dump('拍卖结束');
//                    break;
//                }
//            }
//        }
//    }
//
//    //更新拍卖品状态和拍卖结果
//    function auctionResult($auction_item_id){
//        //查询最后一次出价记录
//        $bid_model = new Bid();
//        $auction_item_model = new AuctionItem();
//        $batch_bid_model = new AutoBid();
//
//
//        $last_bid = $bid_model->getLastBid($auction_item_id);
//        dump('成交出价信息');
//        dump($last_bid->getData());
//
//        switch ($last_bid['type']) {
//            case null://外部流拍
//                $status = 4;
//                break;
//            case 1://已拍出
//                $status = 2;
//                break;
//            case 2://内部流拍
//                $status = 3;
//                break;
//        }
//
//        //更新拍卖品状态
//        $auction_item_model->where('id',$auction_item_id)->setField('status',$status);
//        //一轮竞拍完成,增加1=>未花费的自动竞拍退款,2=>自动竞拍包赔的处理
//        $batch_bid_model->afterAuction($auction_item_id,$last_bid['user_id']);
//        return true;
//    }



//    //伪造出价
//    function falseBid($auction_item_id,$last_bid_user_id,$step=0.1){
//        $user = new User();
//        $false_uid = $user->getFalseUserId($last_bid_user_id);
//        $bid_model = new Bid();
//        //出价
//        $bid_model->biding($false_uid,$auction_item_id,$step,2);
//        dump('伪装出价');
//    }
}