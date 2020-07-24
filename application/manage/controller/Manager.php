<?php
/**
 * Created by PhpStorm.
 * User: xubin
 * Date: 2020/7/1
 * Time: 4:14 PM
 */

namespace app\manage\controller;
use \app\core\ManageController;
use \app\model\member\Basic;
use \app\model\member\ManageUser;
class Manager extends ManageController{

    public function getUserInfo(){

        $memberBasicModel = new Basic();
        $userInfo['user_id'] = $this->_user_id;
        $userInfo['master'] = $this -> master;
        $userInfo['auth_menu'] = $memberBasicModel -> getMain($userInfo);
        $this -> _response['code'] = 0;
        $this -> _response['data'] = $userInfo;
        return $this -> _response;
    }

    public function add(){
        $data['username'] = input('username');
        $data['mobile'] = input('mobile');
        $password = input('password');
        $confirmPassword  = input('confirmPassword');
        $data['user_group_id'] = input('userGroupId');
        $rules = [
            'username'=>'require',
            'mobile'=>'require|max:11',
            'password'=>'require',
            'confirmPassword'=>'require',
            'user_group_id' => 'require'
        ];
        $mes = [
            'username.require'=> '昵称必填',
            'mobile.require'=> '手机号必填',
            'mobile.max'=> '手机号格式错误',
            'password.require'=> '密码必填',
            'confirmPassword.require'=> '确认密码必填',
            'user_group_id.require'=> '管理员组必填',
        ];
        $validate = new \think\Validate($rules,$mes);
        if ($validate->check([
                'username'=>$data['username'],
                'mobile' => $data['mobile'],
                'password'=>$password,
                'confirmPassword' => $confirmPassword,
                'user_group_id' => $data['user_group_id']
            ]) == false) {
            $this->_response['code'] = 10001;
            $this->_response['message'] = $validate->getError();
            return $this->_response;
        }
        if($password != $confirmPassword){
            $this->_response['code'] = 10001;
            $this->_response['message'] = '两次密码不一致';
            return $this->_response;
        }
        $paramResponse = $this -> checkParam($data);
        if($paramResponse){
            $this -> _response['code'] = $paramResponse['code'];
            $this -> _response['message'] = $paramResponse['message'];
            return $this -> _response;
        }
        $manageUserModel = new ManageUser();
        $data['salt'] = rand(000000,999999);
        $data['password'] = md5($data['salt'].$confirmPassword);
        $data['ip'] =  $_SERVER['REMOTE_ADDR'];
        $id = $manageUserModel -> addManager($data);
        $this -> _response['code'] = 0;
        $this -> _response['message'] = 'success';
        $this -> _response['data'] = $id;
        return $this->_response;
    }

    public function  edit(){
        $id = input('id/d');
        $data['username'] = input('username');
        $data['mobile'] = input('mobile');
        $password = input('password');
        $confirmPassword  = input('confirmPassword');
        $data['user_group_id'] = input('userGroupId',0);
        $rules = [
            'id' => 'require',
            'username'=>'require',
            'mobile'=>'require|max:11',
            'password'=>'require',
            'confirmPassword'=>'require',
            'user_group_id' => 'require'
        ];
        $mes = [
            'id.require' => '管理员id必填',
            'username.require'=> '昵称必填',
            'mobile.require'=> '手机号必填',
            'mobile.max'=> '手机号格式错误',
            'password.require'=> '密码必填',
            'confirmPassword.require'=> '确认密码必填',
            'user_group_id.require'=> '管理员组必填',
        ];
        $validate = new \think\Validate($rules,$mes);
        if ($validate->check([
                'username'=>$data['username'],
                'mobile' => $data['mobile'],
                'password'=>$password,
                'confirmPassword' => $confirmPassword,
                'user_group_id' => $data['user_group_id']
            ]) == false) {
            $this->_response['code'] = 10001;
            $this->_response['message'] = $validate->getError();
            return $this->_response;
        }
        $paramResponse = $this -> checkParam($data);
        if($paramResponse){
            $this -> _response['code'] = $paramResponse['code'];
            $this -> _response['message'] = $paramResponse['message'];
            return $this -> _response;
        }
        $manageUserModel = new ManageUser();
        if(!empty($password)){
            if($password != $confirmPassword){
                $this->_response['code'] = 10001;
                $this->_response['message'] = '密码不一致';
                return $this->_response;
            }
            $data['salt'] = rand(000000,999999);
            $data['password'] = md5($data['salt'].$confirmPassword);
        }
        $sta = $manageUserModel -> editManager($id,$data);
        if($sta){
            $this->_response['code'] = 0;
            $this->_response['message'] = 'success';
        }else{
            $this->_response['code'] = -1;
            $this->_response['message'] = 'fail';
        }
        return $this->_response;
    }

    public function del(){
        $id = input('id/d');
        if(empty($id) or $id == 1){
            $this->_response['code'] = 10001;
            $this->_response['message'] = '缺少参数';
            return $this->_response;
        }
        $manageUserModel = new ManageUser();
        $column = 'user_id';
        $value = ['user_id' => $id];
        $memberInfo = $manageUserModel -> getManagerByKey($value,$column);
        if(!$memberInfo){
            $this->_response['code'] = 10001;
            $this->_response['message'] = '用户不存在';
            return $this->_response;
        }
        $manageUserModel -> delManager($id);
        $this->_response['code'] = 0;
        $this->_response['message'] = '删除成功';
        return $this->_response;
    }

    public function userList(){
        $page = input('page/d',0);
        $pageSize = input('pageSize/d',10);
        $page = max($page - 1,0);
        $username = input('username');
        $manageUserModel = new ManageUser();
        $list = $manageUserModel -> getLists($page,$pageSize,$username);
        $this -> _response['code'] = 0;
        $this -> _response['data'] = $list;
        return $this -> _response;
    }

    /*
     * 检查参数是否已存在
     */
    public function checkParam($data){
        $manageUserModel = new ManageUser();
        $column = 'user_id';
        $value = ['username' => $data['username']];
        $usernameExist = $manageUserModel -> getManagerByKey($value,$column);
        if($usernameExist){
            $response['code'] = 10001;
            $response['message'] = '管理员昵称已存在';
            return $response;
        }
        $column = 'user_id';
        $value = ['mobile' => $data['mobile']];
        $mobileExist = $manageUserModel -> getManagerByKey($value,$column);
        if($mobileExist){
            $response['code'] = 10001;
            $response['message'] = '手机号已存在';
            return $response;
        }
    }
}