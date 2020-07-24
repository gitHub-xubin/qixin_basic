<?php
/**
 * Created by PhpStorm.
 * User: xubin
 * Date: 2020/7/19
 * Time: 5:53 PM
 */
namespace app\manage\controller;

use app\core\ManageController;
use app\model\banner\Banner as BannerM;
class Banner extends ManageController{

    public function add(){
        $data['image'] = input('image');
        $data['title'] = input('title');
        $data['type'] = input('type');
        $data['bgcolor'] = input('bgcolor');
        $rules = [
            'image' => "require|number",
            'title' => "require",
            'type' => "require|in:1,2,3",
        ];
        $mes=[
            'image.require' =>  '图片不能为空',
            'image.number' =>  '图片格式错误',
            'title.require' =>  '标题不能为空',
            'type.require' =>  '类型不能为空',
            'type.in' =>  '类型错误',
        ];
        $validate = new \think\Validate($rules,$mes);
        if ($validate->check([
                'image'=>$data['image'],
                'title' => $data['title'],
                'type'=>$data['type']
            ]) == false) {
            $this->_response['code'] = 10001;
            $this->_response['message'] = $validate->getError();
            return $this->_response;
        }
        $model = new BannerM();
        $id = $model -> add($data);
        if($id){
            $this -> _response['code'] = 0;
            $this -> _response['message'] = 'success';
        }else{
            $this -> _response['code'] = -1;
            $this -> _response['message'] = 'fail';
        }
        return $this ->_response;
    }

    public function edit(){
        $id = input('id');
        $data = [
            'image' => input('image'),
            'title' => input('title'),
            'type' => input('type'),
            'bgcolor' => input('bgcolor', null)
        ];
        $rules = [
            'id' => 'require',
            'image' => "require|number",
            'title' => "require",
            'type' => "require|in:1,2,3",
        ];
        $mes=[
            'id.require' => "id不能为空",
            'image.require' =>  '图片不能为空',
            'image.number' =>  '图片格式错误',
            'title.require' =>  '标题不能为空',
            'type.require' =>  '类型不能为空',
            'type.in' =>  '类型错误',
        ];
        $validate = new \think\Validate($rules,$mes);
        if ($validate->check([
                'id' => $id,
                'image'=>$data['image'],
                'title' => $data['title'],
                'type'=>$data['type']
            ]) == false) {
            $this->_response['code'] = 10001;
            $this->_response['message'] = $validate->getError();
            return $this->_response;
        }
        $categoryModel = new BannerM();
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

    public function lists(){
        $model = new BannerM();
        $list = $model -> lists();
        $this -> _response['code'] = 0;
        $this -> _response['data'] = $list;
        return $this -> _response;
    }

    public function del(){
        $id = input('id');
        $model = new BannerM();
        try{
            $model -> del($id);
            $this -> _response['code'] = 0;
            $this -> _response['message'] = 'success';
        }catch (\Exception $e){
            $this -> _response['code'] = -1;
            $this -> _response['message'] = 'fail';
        }
        return $this -> _response;
    }
}