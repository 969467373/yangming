<?php
namespace app\home\controller;

use app\home\model\CnArea;
use app\home\model\CnCity;
use app\home\model\CnProv;
use app\home\model\Goods;
use app\home\model\Store;
use app\home\model\User as usermodel;
use think\Controller;
use think\Db;
use think\Exception;
use think\Request;
use think\Session;
use traits\controller\Jump;

class Index extends Controller
{//首页(签到,论坛,供求,热点,资讯,积分商城)

    public function index()
    {
        //热推店铺
        $storemodel = new Store();
        $store_list = $storemodel->getHotStore();

        //热推商品
        $goodsmodel = new Goods();
        $goods_list = $goodsmodel->getHotGoods();

        $view=[
            'store_list'=>$store_list['data'],
            'goods_list'=>$goods_list['data'],
        ];

        return view('index',$view);


    }

    //获取省
    public function prov(){
        $model = new CnProv();
        $prov = $model->getAllProv();

        $view=[
            'prov'=>$prov['data'],
        ];
        return view('city',$view);
    }

    //ajax获取市
    public function city(){

        try{
            $code = request()->param('code');

            $model = new CnCity();
            $city = $model->getCity($code);

            return['code'=>200,'message'=>'成功','city'=>$city['data']];
        }catch (\Exception $e){
            return['code'=>400,'message'=>'失败=>'.$e->getMessage()];
        }
    }


    //ajax获取区
    public function area()
    {
        try{
            $code = request()->param('code');

            $model = new CnArea();
            $area = $model->getArea($code);

            return['code'=>200,'message'=>'成功','area'=>$area['data']];
        }catch (\Exception $e){
            return['code'=>400,'message'=>'失败=>'.$e->getMessage()];
        }
    }

    //ajax三级联动
    public function ajaxGetAdress(){
        try{
            $data = request()->param();

            if($data['type'] == "prov"){
                //初始值
                $str = '<option value="-1">--请选择市--</option>';

                $model = new CnCity();
                $address = $model->getCity($data['code']);
            }else{
                //初始值
                $str = '<option value="-1">--请选择区--</option>';

                $model = new CnArea();
                $address = $model->getArea($data['code']);
            }

            //遍历数组转换字符串链接
            foreach ($address['data'] as $key => $value) {

                $str .= '<option value="'.$value["code"].'">'.$value["name"].'</option>';
            }

            return['code'=>200,'message'=>'成功','type'=>$data['type'],'str'=>$str];
        }catch (\Exception $e){
            return['code'=>400,'message'=>'失败=>'.$e->getMessage()];
        }
        
        
        
    }
    
}
