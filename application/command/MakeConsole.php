<?php
namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\Db;
use think\Exception;


class MakeConsole extends Command
{
    protected function configure()
    {
        $this->setName('MakeConsole')->setDescription('使用命令行生成后台功能表(表名固定为console,后台模块指定为console)');
    }


    protected function execute(Input $input, Output $output)
    {
        $files = scandir ( APP_PATH.'console'.DS.'controller');
        foreach ($files as $file){
            if (!in_array($file,['.','..'])){
                $data = file_get_contents(APP_PATH.'console'.DS.'controller'.DS.$file);
                $class_match = [];
                $function_match = [];
                preg_match_all('/class\s+(\w+)\s?/', $data, $class_match);
                preg_match_all('/@#\s+(.*)\s?/', $data, $function_match);
                $controller = $class_match[1][0];
                $function_match_data = $function_match[1];
                if (!empty($controller)&&!empty($function_match_data)){
                    foreach ($function_match_data as $function_item){
                        list($level_one,$level_two,$function_name,$action) = explode('-',$function_item);
                        $data_insert[] = [
                            'level_one'=>$level_one,
                            'level_two'=>$level_two,
                            'function_name'=>$function_name,
                            'controller'=>$class_match[1][0],
                            'action'=>rtrim($action),
                            'type'=>$level_two==$function_name?1:2,
                        ];
                    }
                }
            }
        }
        Db::startTrans();
        try{

            Db::execute("truncate table lc_console");
            Db::name('console')->insertAll($data_insert);
            Db::commit();
        }catch (Exception $e){
            Db::rollback();
            halt($e->getMessage());
        }

    }
}