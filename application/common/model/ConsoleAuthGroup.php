<?php

namespace app\common\model;


class ConsoleAuthGroup extends BaseModel
{
    function setConsoleIdsAttr($value)
    {
        return serialize($value);
    }

    function getConsoleIdsAttr($value)
    {
        return unserialize($value);
    }

    //获取后台权限id数组
    function getConsoleIds($id){
        $data = $this->where('id',$id)->value('console_ids');
        return empty($data)?[]:unserialize($data);
    }

    //新增权限组
    function add($data){
        return $this->save($data);
    }
    //更新限组
    function edit($data){
        return $this->isUpdate(true)->save($data);
    }

    //后台分页
    function consoleGetListByWhere($where)
    {
        return $this->where($where)
            ->field(['id', 'name'])
            ->paginate(20, false, ['query' => request()->get()]);
    }
    //按id删除
    function deleteById($id)
    {
        return $this->where('id',$id)->delete();
    }
}
