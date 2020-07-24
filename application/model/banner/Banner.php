<?php
/**
 * Created by PhpStorm.
 * User: xubin
 * Date: 2020/6/26
 * Time: 12:18 PM
 */
namespace app\model\banner;
use \app\core\ApiModel;

class Banner extends ApiModel{

    public function lists(){
        $sql = "SELECT b.*, f.file_url as image FROM `banner` as b left join `file_lib` as f on b.image = f.id ";
        $list = ApiModel::findAll($sql);
        foreach ($list as $k => $v){
            $list[$k]['image'] = serverName().$v['image'];
        }
        return $list;
    }
    public function add($data){
        $sql = "insert into `banner` (image, title, type, bgcolor)values (?,?,?,?)";
        \think\Db::execute($sql, [$data['image'], $data['title'], $data['type'], $data['bgcolor']]);
        return \think\Db::getLastInsID();
    }
    public function edit($id, $data){
        $set = [];
        foreach ($data as $key => $value) {
            if ($value!=null or $value === 0) {
                $set[] = "`{$key}`= '$value'";
            } else {
                $set[] = "`{$key}`= null";
            }
        }
        $set = implode(",", $set);
        $sql = "UPDATE `banner` SET {$set} WHERE `id`=?";
        $rs = \think\Db::execute($sql, [$id]);
        return $rs;
    }
    public function del($id){
        $sql = "delete from `banner` where id = ? ";
        \think\Db::execute($sql, [$id]);
        return true;
    }
}