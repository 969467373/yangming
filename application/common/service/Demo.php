<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/6 0006
 * Time: 15:09
 */
namespace app\common\service;


use think\Db;

class Demo
{

    /**
     * service层用于执行多表操作,一般需要使用事务
     * @param $data
     * @return bool
     */
    static function demo($data)
    {
        //开启事务
        Db::startTrans();
        try {
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return false;
        }
    }

}