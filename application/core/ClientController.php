<?php

namespace app\core;

use think\Controller;

/**
 * 客户端
 *
 * @author
 */
class ClientController extends Controller {

    /**
     * 定义返回格式
     * @var array 
     */
    protected $_response = ['code' => 0, 'message' => '', 'data' => ''];
    protected $_user_id;
    protected $token;
    public function initialize() {

        $_m     = $this->request->module();
        $_c = strtolower($this->request->controller());
        $_a = strtolower($this->request->action());

        //无需验证path，不需要Authorization
        $_filter = [
            'logon' => ['logon'],
        ];
        //公共path,需要Authorization,但不验证权限
        $public_permission = [
            'logon' => ['getuserinfo'],
        ];

        if (isset($_filter[$_c])) {
            $_f_v = $_filter[$_c];
            if ($_f_v == '*') {
                return true;
            }
            if (in_array($_a, $_f_v)) {
                return true;
            }
        }
        return true;
        $token = $this->request->header('Authorization');
        if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
            $this->_response['code']    = 0;
            $this->_response['message'] = $this->language['success'];
            echo json_encode($this->_response);
            exit;
        }
        if (empty($token)) {
            $this->_response['code']    = 10100;
            $this->_response['message'] = $this->language['invalidUser'];//'不合法用户';
            echo json_encode($this->_response);
            exit;
        }
        /*$memberBasicModel = new \app\model\member\Basic();
        $decodeToken = $memberBasicModel -> check($token);
        if($decodeToken){
            $decodeToken = (array)$decodeToken;
            $this -> _user_id = $decodeToken['user_id'];
            $this -> token = $token;
            $this -> store_id = $decodeToken['store_id'];
            $userBasicModel = new \app\model\member\Basic();
            $value = ['user_id' => $this -> _user_id];
            $col = 'master';
            $userInfo = $userBasicModel -> getUserByKey($value,$col);
            if($userInfo['master'] == 1){
                $permission = $userBasicModel -> getMainPermission($this -> store_id);
                $path = [];
                if(!empty($permission)){
                    foreach ($permission as $k => $v){
                        $path[] = strtolower($v['node']);
                    }
                }
            }else{
                $permission = $userBasicModel -> getPermission($this -> _user_id);
                $path = [];
                foreach ($permission as $k => $v){
                    $path[] = strtolower($v['node']);
                }
            }
            if (isset($public_permission[$_c])) {
                $_f_v = $public_permission[$_c];
                if ($_f_v == '*') {
                    return true;
                }
                if (in_array($_a, $_f_v)) {
                    return true;
                }
            }
            $currentPath = $_m.'/'.$_c.'/'.$_a;
            if(!in_array($currentPath,$path)){
                $this -> _response['code'] = 1003;
                $this -> _response['message'] = $this->language['noPermission'];//'无权限';
                echo json_encode($this->_response);
                exit();
            }
        }else{
            $this->_response['code']    = 1000;
            $this->_response['message'] = $this->language['loginAgain'];//'请重新登录';
            echo json_encode($this->_response);
            exit();
        }*/
    }

    /**
     * 异常信息收集
     * @param Exception $e
     */
    public function _error($e) {
        $this->_response['code']    = $e->getCode();
        $this->_response['message'] = $e->getMessage();
    }

}
