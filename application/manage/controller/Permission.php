<?php
/**
 * Created by PhpStorm.
 * User: xubin
 * Date: 2019/8/21
 * Time: 4:30 PM
 */
namespace app\manage\controller;
use \app\core\ManageController;
use \app\model\member\Basic;
class Permission extends ManageController{

    //管理员组
    public function userGroupList(){
        $memberBasicModel = new Basic();
        $res = $memberBasicModel -> userGroupList();
        $this -> _response['code'] = 0;
        $this -> _response['data'] = $res;
        return $this -> _response;
    }

    //权限列表
    public function permissionList(){
        $userGroupId  = input('groupId',null);
        $memberBasicModel = new Basic();
        $res = $memberBasicModel -> getPerMissionList($userGroupId);
        $this -> _response['code'] = 0;
        $this -> _response['data'] = $res;
        return $this -> _response;
    }

    public function changeUserGroup(){
        $userGroupId  = input('groupId',null);
        $permission = input('permission',null);
        $name = trim(input('name',null));
        if(empty($userGroupId) or empty($permission) or empty($name)){
            $this -> _response['code'] = 10001;
            $this -> _response['message'] = '缺少参数';
            return  $this -> _response;
        }
        $userBasicModel = new Basic();
        $value = ['name' => $name];
        $where_s = "user_group_id <> ".$userGroupId;
        $col = 'user_group_id';
        $info = $userBasicModel -> getUserGroupByKey($value,$col,$where_s);
        if(!empty($info)){
            $this -> _response['code'] = 10001;
            $this -> _response['message'] = '小组已占用';
            return $this -> _response;
        }
        if(empty(json_decode($permission,TRUE))){
            $this -> _response['code'] = 10001;
            $this -> _response['message'] = '参数错误';
            return $this -> _response;
        }
        $permissionArr = json_decode($permission,TRUE);
        if(count($permissionArr) != count($permissionArr,1)){
            $this -> _response['code'] = 10001;
            $this -> _response['message'] = '参数错误';
            return $this -> _response;
        }
        $memberBasicModel = new Basic();
        $data = [
            'name' => $name,
            'permission' => $permission,
        ];
        $memberBasicModel -> editUserGroupPermission($userGroupId,$data);
        $this -> _response['code'] = 0;
        $this -> _response['message'] = 'success';
        return $this -> _response;
    }

    public function addUserGroup(){
        $name = trim(input('name'));
        $permission = input('permission');
        if(empty($name) or empty($permission)){
            $this -> _response['code'] = 10001;
            $this -> _response['message'] = '缺少参数';
            return $this -> _response;
        }
        $userBasicModel = new Basic();
        $value = ['name' => $name];
        $col = 'user_group_id';
        $info = $userBasicModel -> getUserGroupByKey($value,$col);
        if(!empty($info)){
            $this -> _response['code'] = 10001;
            $this -> _response['message'] = '小组已占用';
            return $this -> _response;
        }
        if(empty(json_decode($permission,TRUE))){
            $this -> _response['code'] = 10001;
            $this -> _response['message'] = '参数错误';
            return $this -> _response;
        }
        $memberBasicModel = new Basic();
        $data = [
            'name' => $name,
            'permission' => $permission
        ];
        $userGroupId = $memberBasicModel -> addUserGroup($data);
        $this -> _response['code'] = 0;
        $this -> _response['message'] = 'success';
        $this -> _response['data'] = $userGroupId;
        return $this -> _response;
    }

    public function delUserGroup(){
        $userGroupId = input('groupId');
        if(empty($userGroupId)){
            $this -> _response['code'] = 10001;
            $this -> _response['message'] = '缺少参数';
            return $this -> _response;
        }
        $userBasicModel = new Basic();
        $value = ['user_group_id' => $userGroupId];
        $col = 'name';
        $info = $userBasicModel -> getUserGroupByKey($value,$col);
        if(empty($info)){
            $this -> _response['code'] = 10001;
            $this -> _response['message'] = '参数错误';
            return $this -> _response;
        }
        $value_s = ['user_group_id' => $userGroupId];
        $column = 'user_id';
        $user = $userBasicModel -> getUserByKey($value_s,$column);
        if(!empty($user)){
            $this -> _response['code'] = 10001;
            $this -> _response['message'] = '参数错误';
            return $this -> _response;
        }
        $userBasicModel -> del($userGroupId);
        $this -> _response['code'] = 0;
        $this -> _response['message'] ='success';
        return $this -> _response;
    }
}