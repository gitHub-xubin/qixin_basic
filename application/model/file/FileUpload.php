<?php
/**
 * Created by PhpStorm.
 * User: xubin
 * Date: 2020/7/5
 * Time: 3:12 PM
 */
namespace app\model\file;

use app\core\ApiModel;

class FileUpload extends ApiModel{

    public function add($data){
        $sql = "insert into `file_lib` (file_url,add_time)values(?,?)";
        \think\Db::execute($sql, [$data['file_url'], systemTime()]);
        return true;
    }

    public function getList($page=0, $pageSize=10){
        $sql = "select id,file_url from `file_lib`";
        $count_sql = "select count(*) as count from `file_lib`";
        $list = ApiModel::_getList($sql, $count_sql, null, null, 1, $page, $pageSize);
        foreach ($list['list'] as $k => $v){
            $list['list'][$k]['file_url'] = serverName().$v['file_url'];
        }
        return $list;
    }

    public function del($id){
        $sql = "delete from `file_lib` where id = ?";
        \think\Db::execute($sql, [$id]);
        return true;
    }

    public function getOne($id){
        $sql = "select * from `file_lib` where id = ?";
        $list = ApiModel::find($sql,[$id]);
        return $list;
    }
}