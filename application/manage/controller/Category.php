<?php
/**
 * Created by PhpStorm.
 * User: xubin
 * Date: 2020/7/8
 * Time: 10:54 AM
 */
namespace app\manage\controller;

use app\core\ManageController;
use app\model\category\Category as CategoryM;
class Category extends ManageController{

    //添加
    public function add() {
        $data = [
            'parent_id' => input('parent_id', 0),
            'name' => input('name'),
            'is_recommend' => input('isRecommend',0),
            'image' => input('image',null)
        ];
        if(empty($data['name'])){
            $this -> _response['code'] = 10001;
            $this -> _response['message'] = '缺少参数';
            return $this -> _response;
        }
        $categoryModel = new CategoryM();
        try {
            $rs = $categoryModel->add($data);
            if ($rs) {
                $this->_response['code'] = 0;
                $this->_response['message'] = 'ok！';
            }
        } catch (\Exception $e) {
            $this->_response['code'] = -1;
            $this->_response['message'] = 'fail';
        }
        return $this->_response;
    }

    //编辑
    public function edit(){
        $id = input('id');
        $data = [
            'parent_id' => input('parentId', 0),
            'name' => input('name'),
            'is_recommend' => input('isRecommend', 0),
            'image' => input('image', null)
        ];
        if(empty($data['name']) or empty($id)){
            $this -> _response['code'] = 10001;
            $this -> _response['message'] = '缺少参数';
            return $this -> _response;
        }
        $categoryModel = new CategoryM();
        try {
            $categoryModel->edit($id, $data);
            $this -> _response['code'] = 0;
            $this -> _response['message'] = 'ok';
        } catch (\Exception $e) {
            $this -> _response['code'] = -1;
            $this -> _response['message'] = 'fail';
        }
        return $this -> _response;
    }

    //获取所有
    public function getLists() {
        try {
            $categoryModel = new CategoryM();
            $data = $categoryModel->getLists();
            $this->_response['message'] = 'ok！';
            $this->_response['data'] = $data;
        } catch (\Exception $e) {
            $this->_error($e);
        }
        return $this->_response;
    }

    //删除
    public function delete() {
        $id = input('id');
        $categoryModel = new CategoryM();
        try {
            $rs = $categoryModel->del($id);
            if ($rs) {
                $this->_response['code'] = 0;
                $this->_response['message'] = 'ok！';
            }
        } catch (\Exception $e) {
            $this->_error($e);
        }
        return $this->_response;
    }

    //设置是否推荐
    public function setRecommend() {

        $isRecommend = input('isRecommend');
        $id = input('id');
        $data['is_recommend'] = $isRecommend;
        $categoryModel = new CategoryM();
        try {
            $rs = $categoryModel->edit($id, $data);
            if ($rs) {
                $this->_response['code'] = 0;
                $this->_response['message'] = 'ok！';
            }
        } catch (\Exception $e) {
            $this->_error($e);
        }
        return $this->_response;
    }
}