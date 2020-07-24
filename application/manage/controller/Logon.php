<?php
/**
 * Created by PhpStorm.
 * User: xubin
 * Date: 2020/6/30
 * Time: 9:56 AM
 */
namespace app\manage\controller;
use \think\Validate;
use \app\model\member\Basic;
class Logon extends \app\core\ManageController{

    public function Logon(){
        $mobile = input('mobile');
        $password = input('password');

        $rule = [
            'mobile'=>'require|max:11',
            'password'=>'require'
        ];
        $mes = [
            'mobile.require' => '手机号不能为空',
            'mobile.max' => '手机号错误',
            'password' => '密码不能为空'
        ];
        $validate = new Validate($rule,$mes);
        if ($validate->check(['mobile'=>$mobile,'password'=>$password]) == false) {
            $this->_response['code'] = 10001;
            $this->_response['message'] = $validate->getError();
            return $this->_response;
        }
        $memberBasicModel = new Basic();
        $column = 'user_id,username,mobile,password,salt,master';
        $value = ['mobile' => $mobile];
        $userInfo = $memberBasicModel -> getUserByKey($value,$column);
        if(empty($userInfo)){
            $this->_response['code'] = 10002;
            $this->_response['message'] = '用户不存在';
            return $this->_response;
        }
        if(md5($userInfo['salt'].$password) != $userInfo['password']){
            $this->_response['code'] = 10002;
            $this->_response['message'] = '密码错误';
            return $this->_response;
        }
        $userInfo['token'] = $memberBasicModel -> getManageToken($userInfo);
        $token = $userInfo['token'];
        $data['ip'] = $_SERVER['SERVER_ADDR'];
        unset($userInfo['password'],$userInfo['salt']);
        $memberBasicModel -> editUser($userInfo['user_id'],$data);
        $this -> _response['code'] = 0;
        $this -> _response['data'] = $token;
        return $this -> _response;
    }
}