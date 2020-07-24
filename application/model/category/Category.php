<?php
/**
 * Created by PhpStorm.
 * User: xubin
 * Date: 2020/7/7
 * Time: 11:23 PM
 */
namespace app\model\category;

use app\core\ApiModel;

class  Category extends ApiModel{

    public function add($data){
        $sql = 'insert into `category` (name, image, parent_id, is_recommend) values (?,?,?,?)';
        \think\Db::execute($sql, [$data['name'], $data['image'], $data['parent_id'], $data['is_recommend']]);
        return \think\Db::getLastInsID();
    }

    //获取一条
    public function getOne($id) {
        $sql = "SELECT c.*, f.file_url as image FROM `category` as c left join `file_lib` as f on c.image = f.id WHERE c.`id`=? LIMIT 1";
        $categoryInfo = $this->find($sql, [$id]);
        $categoryInfo['image'] = serverName().$categoryInfo['image'];
        return $categoryInfo;
    }

    //获取子分类
    public function getSubclassCategory($parent_id) {
        $sql = "SELECT c.*, f.file_url as image FROM `category` as c left join `file_lib` as f on c.image = f.id where  c.parent_id =?";
        $list = $this->findAll($sql, [$parent_id]);
        foreach ($list as $k => $v){
            $list[$k]['image'] = serverName().$v['image'];
        }
        return $list;
    }

    //获取一级分类
    public function getFirstAll() {
        $sql = "SELECT c.*, f.file_url as image FROM `category` as c left join `file_lib` as f on c.image = f.id where parent_id =0";
        $list = $this->findAll($sql);
        foreach ($list as $k => $v){
            $list[$k]['image'] = serverName().$v['image'];
        }
        return $list;
    }

    //获取所有
    public function getLists() {

        $sql = "SELECT c.*, f.file_url as image FROM `category` as c left join `file_lib` as f on c.image = f.id ";
        $data = $this->findAll($sql);
        $_data = [];
        foreach ($data as $k => $v) {
            if ($v['parent_id'] == 0) {
                $_data[] = $v;
            }
        }
        $_data_one = [];
        foreach ($_data as $kk => $vv) {
            foreach ($data as $key => $value) {
                if ($vv['id'] == $value['parent_id']) {
                    $vv['subclass'][] = $value;
                }
            }
            $_data[$kk]['image'] = serverName().$vv['image'];
            $_data_one[] = $vv;
        }

        $_data = array();
        foreach ($_data_one as $k => $v) {
            if (!empty($v['image'])) {
                $v['image'] = serverName(). $v['image'];
            }
            $_data[] = $v;
        }
        $return_data['list'] = $_data;
        return $return_data;
    }

    public function edit($id, $data) {
        $set = [];
        foreach ($data as $key => $value) {
            if ($value!=null or $value === 0) {
                $set[] = "`{$key}`= '$value'";
            } else {
                $set[] = "`{$key}`= null";
            }
        }
        $set = implode(",", $set);
        $sql = "UPDATE `category` SET {$set} WHERE `id`=?";
        $rs = \think\Db::execute($sql, [$id]);
        return $rs;
    }

    public function del($id){
        $sql = "delete from `category` where id = ?";
        \think\Db::execute($sql,[$id]);
        return true;
    }
}