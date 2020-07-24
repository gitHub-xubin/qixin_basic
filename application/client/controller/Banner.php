<?php
/**
 * Created by PhpStorm.
 * User: xubin
 * Date: 2020/6/26
 * Time: 11:57 AM
 */
namespace app\client\controller;
use \app\model\banner\Banner as BannerModel;
class Banner extends \app\core\ClientController{

    public function bannerList(){
        $bannerModel = new BannerModel();
        $this -> _response['data'] = $bannerModel->bannerList();
        return $this -> _response;
    }
}