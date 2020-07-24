<?php
/**
 * Created by PhpStorm.
 * User: xubin
 * Date: 2019/9/10
 * Time: 10:59 AM
 */
namespace app\model\member;

use app\core\ApiModel;

class ManageUser extends \app\core\ApiModel{

    /**
     * 获取管理后台用户
     * @param $value  array
     * @param $column   string
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getManagerByKey($value,$column,$where_s = ''){
        $where = [];
        foreach ($value as $k => $v){
            $where[] = "$k = '$v'";
        }
        $sql_where = implode( " and " , $where);
        if($where_s){
            $sql_where = $sql_where.' and '.$where_s;
        }
        $sql = "select $column from `manager` where $sql_where";
        $result =  ApiModel::find($sql);
        return $result;
    }

    /**
     * 编辑Manage
     * @param $id
     * @param $data
     * @return int
     */
    public function editManager($id, $data) {

        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "`{$key}`='{$value}'";
        }
        $set = implode(',', $set);

        $sql = "UPDATE `manager` SET {$set} WHERE `user_id`=?";

        \think\Db::execute($sql, [$id]);
        return true;
    }

    /**
     * 添加账户
     * @param $data
     * @return string
     */
    public function addManager($data){
        $time = date('Y-m-d H:i:s');
        $sql = "insert into `manager` (user_group_id,username,password,salt,mobile,ip,add_time)
                values (?,?,?,?,?, ?,?)";
        \think\Db::execute($sql,[$data['user_group_id'],$data['username'],$data['password'],$data['salt'],$data['mobile'],$data['ip'],$time]);
        return \think\Db::getLastInsID();
    }

    /**
     * 账号列表
     * @param int $page
     * @param int $pageSize
     * @param string $username
     * @return array|mixed
     */
    public function getLists($page = 0, $pageSize = 10,$username = '') {
        $where = [];
        $order = ['u.user_id DESC'];
        if ($username) {
            $where[] = "(u.username like '%{$username}%')";
        }
        $sql       = "SELECT u.user_id,u.username,u.user_group_id,u.mobile,u.add_time, ug.name as group_name FROM `manager` as u left join  `manager_group` as ug 
                      ON u.user_group_id = ug.user_group_id";
        $sql_count = "SELECT count(*) as `count` FROM `manager` as u left join  user_group as ug 
                      ON u.user_group_id = ug.user_group_id ";
        $return    = ApiModel::_getList($sql, $sql_count, $where, $order, true, $page, $pageSize);
        return $return;
    }

    /**
     * 删除manage_user
     * @param $id
     * @return bool
     */
    public function delManager($id){
        $sql = "delete from `manager` where user_id = ?";
        \think\Db::execute($sql,[$id]);
        return true;
    }
}