<?php
/**
 * Created by PhpStorm.
 * User: xubin
 * Date: 2020/7/4
 * Time: 3:52 PM
 */
namespace app\manage\controller;

use app\core\ManageController;
use think\facade\Env;
use \app\model\file\FileUpload;
class FileLib extends ManageController{
    const IMG_SIZE = 1024000;
    const IMG_TYPE = 'jpg,png,jpeg';
    public function add(){
        $image = request()->file('image');
        if(empty($image)){
            $this->_response['code'] = 10001;
            $this->_response['message'] = '请上传图片';
            return $this->_response;
        }
        $info = $image->validate(['size'=>self::IMG_SIZE,'ext'=>self::IMG_TYPE])->move( Env::get('ROOT_PATH'). '/public/uploads/images');
        if ($info) {
            $data['file_url'] = '/uploads/images/'.$info->getSaveName();
        } else {
            $this->error($image->getError());
            $this->_response['code'] = -3;
            $this->_response['message'] = $info->getError();
            return $this->_response;
        }
        $model =new  FileUpload();
        $model -> add($data);
        $this->_response['code'] = 0;
        $this->_response['message'] = 'ok';
        return $this->_response;
    }

    public function lists(){
        $page = input('page/d',0);
        $page = max($page -1, 0);
        $pageSize = input('pageSize/d',10);

        $model = new FileUpload();
        $list = $model -> getList($page, $pageSize);
        $this -> _response['code'] = 0;
        $this  -> _response['data'] = $list;
        return $this -> _response;
    }

    public function del(){
        $id = input('id');
        $model = new FileUpload();
        $info = $model -> getOne($id);
        if(empty($info)){
            $this -> _response['code'] = 10001;
            $this -> _response['message'] = '参数错误';
            return $this -> _response;
        }
        $tmp = $model -> del($id);
        if($tmp){
            unlink(Env::get('ROOT_PATH'). '/public'.$info['file_url']);
        }
        $this->_response['code'] = 0;
        $this->_response['message'] = 'ok';
        return $this->_response;
    }
}