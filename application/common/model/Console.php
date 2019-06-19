<?php

namespace app\common\model;


class Console extends BaseModel
{
    function getConsoleData(){
        $console_data = [];
        $all =$this->order('type asc')->select();
        foreach ($all as $item){
            $console_data[$item['level_one']]['level_one'] = $item['level_one'];

            $data = [
                'title'=>$item['function_name'],
                'id'=>$item['id'],
                'type'=>$item['type']
            ];
            $console_data[$item['level_one']]['level_two'][$item['level_two']][] = $data;
        }
        return $console_data;
    }
}
