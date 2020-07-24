<?php
/**
 * Created by PhpStorm.
 * User: xubin
 * Date: 2019/5/16
 * Time: 5:11 PM
 */
namespace app\model\member;
use app\core\ApiModel;
use Firebase\JWT\JWT;
use \think\facade\Env;
require_once Env::get('extend_path').'/jwt/JWT.php';
class Basic extends \app\core\ApiModel{

    /**
     * JWT 生成加密token
     * @param $userInfo
     * @return string
     */
    public function getToken($userInfo){
        $key = config('jwt');  //这里是自定义的一个随机字串，应该写在config文件中的，解密时也会用，相当    于加密中常用的 盐  salt
        $token = [
            "iss"=>"qixin",  //签发者 可以为空
            "aud"=>$userInfo['userName'], //面象的用户，可以为空
            "iat" => time(), //签发时间
            "nbf" => '', //非必须。not before。如果当前时间在nbf里的时间之前，则Token不被接受；一般都会留一些余地，比如几分钟。
            "exp" => time()+15*24*3600, //token 过期时间
            "user_id" => $userInfo['user_id'], //自定义字段，用户id
        ];
        $jwt = JWT::encode($token,$key,"HS256"); //根据参数生成了 token
        return $jwt;
    }

    /**
     * JWT 生成加密token
     * @param $userInfo
     * @return string
     */
    public function getManageToken($userInfo){
        $key = config('jwt');  //这里是自定义的一个随机字串，应该写在config文件中的，解密时也会用，相当    于加密中常用的 盐  salt
        $token = [
            "iss"=>"qinxin",  //签发者 可以为空
            "aud"=>$userInfo['username'], //面象的用户，可以为空
            "iat" => time(), //签发时间
            "nbf" => '', //非必须。not before。如果当前时间在nbf里的时间之前，则Token不被接受；一般都会留一些余地，比如几分钟。
            "exp" => time()+15*24*3600, //token 过期时间
            "user_id" => $userInfo['user_id'], //自定义字段，用户id
            "master" => $userInfo['master']
        ];
        $jwt = JWT::encode($token,$key,"HS256"); //根据参数生成了 token
        return $jwt;
    }

    /**
     * JWT 解密token
     * @param $token
     * @return object
     */
    public function check($token){
        $key = config('jwt');
        $info = JWT::decode($token,$key,["HS256"]); //解密jwt
        return $info;
    }

    /**
     * 获取用户
     * @param $value  array
     * @param $column   string
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getUserByKey($value,$column,$where_s = ''){
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
     * 添加账户
     * @param $data
     * @return string
     */
    public function addUser($data){
        $time = date('Y-m-d H:i:s');
        $sql = "insert into `manager` (user_group_id,store_id,username,password,salt,email,ip,date_added,master,team_id)
                values (?,?,?,?,?,?,?,?,?,?)";
        \think\Db::execute($sql,[$data['user_group_id'],$data['store_id'],$data['username'],$data['password'],$data['salt'],$data['email'],$data['ip'],$time,$data['master'],$data['team_id']]);
        return \think\Db::getLastInsID();
    }

    /**
     * 编辑User
     * @param $id
     * @param $data
     * @return int
     */
    public function editUser($id, $data) {
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
     * 删除User
     * @param $id
     * @return bool
     */
    public function delUser($id){
        $sql = "delete from `manager` where user_id = ?";
        \think\Db::execute($sql,[$id]);
        return true;
    }

    //获取用户权限菜单
    public function getMain($userInfo){
        if($userInfo['master'] == 1){
            $sql = "select node_id,name,title from `node` where level = 1 ";
            $top = $this->findAll($sql);
            foreach ($top as $k => $v) {
                $sql = "select node_id,name,title from `node` where level = 2 and parent_id = " . $v['node_id'];
                $sec = $this->findAll($sql);
                $top[$k]['second'] = $sec;
            }
        }else{
            $sql = "select  up.permission from `manager` as u 
                left join `manager_group` as up on u.user_group_id = up.user_group_id 
                where u.user_id = ?";
            $result = ApiModel::find($sql,[$userInfo['user_id']]);
            $top = [];
            $permission = json_decode($result['permission'],true);
            if(!empty($permission)){
                $permissionStr = implode($permission,',');
                $sql = "select node_id,name,title  from `node` where node_id in ($permissionStr) and level = 1";
                $top = ApiModel::findAll($sql);
                foreach ($top as $k => $v){
                    $sql = "select node_id,name,title from `node` where parent_id = ".$v['node_id']." and level = 2";
                    $second = ApiModel::findAll($sql);
                    $top[$k]['second'] = $second;
                }
            }
        }
        return $top;
    }

    //获取用户权限
    public function getPermission($userId, $level=3){
        $sql = "select up.permission from `manager` as u 
                left join `manager_group` as up on u.user_group_id = up.user_group_id 
                where u.user_id = ?";
        $result = ApiModel::find($sql,[$userId]);
        if(!empty($result['permission'])) {
            $permission = json_decode($result['permission'], true);
            $permissionStr = implode($permission, ',');
            $sql = "select * from `node` where node_id in ($permissionStr) and level = $level";
            $result = ApiModel::findAll($sql);
        }
        return $result;
    }

    //用户组
    public function userGroupList(){
        $sql = "select * from  `manager_group` ";
        $result = ApiModel::findAll($sql);
        foreach ($result as $k => $v){
            $permission = json_decode($v['permission'],true);
            $permissionStr = implode($permission,',');
            $sql = "select node_id,name from `node` where node_id in ($permissionStr) and level = 2";
            $per = ApiModel::findAll($sql);
            foreach ($per as $key => $val){
                $sql = "select name from `node` where parent_id = ".$val['node_id'];
                $third = ApiModel::findAll($sql);
                $perName = [];
                foreach ($third as $id => $ids){
                    $perName[] = $ids['name'];
                }
                $per[$key]['third'] = $perName;
            }
            $result[$k]['permission'] = $per;
        }
        return $result;
    }

    public function getPerMissionList($userGroupId = null){

            $sql = "select node_id,name,title from `node` where level = 1";
            $permission = ApiModel::findAll($sql);
            if ($userGroupId != null) {
                $sql = "select user_group_id,permission from `manager_group` where user_group_id = ?";
                $result = ApiModel::find($sql, [$userGroupId]);
                $userGroupPermission = json_decode($result['permission'], true);
            }
            foreach ($permission as $k => $v) {
                if (!empty($userGroupPermission)) {
                    if (in_array(strtolower($v['node_id']), $userGroupPermission)) {
                        $permission[$k]['is_auth'] = 1;
                    } else {
                        $permission[$k]['is_auth'] = 0;
                    }
                }
                $sql = "select node_id,name,title from `node` where level = 2  and parent_id = " . $v['node_id'];
                $secondPermission = ApiModel::findAll($sql);

                foreach ($secondPermission as $key => $val) {
                    if (!empty($userGroupPermission)) {
                        if (in_array(strtolower($val['node_id']), $userGroupPermission)) {
                            $secondPermission[$key]['is_auth'] = 1;
                        } else {
                            $secondPermission[$key]['is_auth'] = 0;
                        }
                    }
                    $sql = "select node_id,name,title from `node` where level = 3 and  parent_id = " . $val['node_id'];
                    $thirdPermission = ApiModel::findAll($sql);
                    foreach ($thirdPermission as $id => $idx) {
                        if (!empty($userGroupPermission)) {
                            if (in_array(strtolower($idx['node_id']), $userGroupPermission)) {
                                $thirdPermission[$id]['is_auth'] = 1;
                            } else {
                                $thirdPermission[$id]['is_auth'] = 0;
                            }
                        }
                    }
                    $secondPermission[$key]['third'] = $thirdPermission;
                }
                $permission[$k]['second'] = $secondPermission;
            }

            return $permission;
    }

    //修改用户组
    public function editUserGroupPermission($id, $data) {

        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "`{$key}`='{$value}'";
        }
        $set = implode(',', $set);

        $sql = "UPDATE `manager_group` SET {$set} WHERE `user_group_id`=?";
        \think\Db::execute($sql, [$id]);
        return true;
    }

    //添加用户权限组
    public function addUserGroup($data){
            $sql = "insert into `manager_group` (name,permission) values (?,?)";
            \think\Db::execute($sql,[ $data['name'], $data['permission']]);
            return \think\Db::getLastInsID();
    }

    //获取单条权限组
    public function getUserGroupByKey($value,$column,$where_s=null){
            $where = [];
            foreach ($value as $k => $v){
                $where[] = "$k = '$v'";
            }
            $sql_where = implode( " and " , $where);
            if($where_s){
                $sql_where = $sql_where.' and '.$where_s;
            }
            $sql = "select $column from `manager_group` where $sql_where";
            $result =  ApiModel::find($sql);
            return $result;
    }
    //删除用户组
    public function del($userGroupId){
        $sql = "delete from `manager_group` where user_group_id = ?";
        \think\Db::execute($sql,[$userGroupId]);
        return true;
    }
}