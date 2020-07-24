<?php
/**
 * Created by PhpStorm.
 * User: xubin
 * Date: 2020/7/7
 * Time: 10:11 PM
 */
namespace app\manage\controller;

use app\core\ManageController;

class Product extends ManageController{

    public function add(){

        $data['name'] = input('name');
        $data['description'] = input('description');
        $data['image'] = input('image');
        $data['tag'] = input('tag');
        $data['quantity'] = input('quantity');
        $data['sort_order'] = input('sortOrder');
        $data['price'] = input('price');
        $data['category_id'] = input('categoryId');
        $data['is_discount'] = input('isDiscount'); //是否优惠商品
        $data['is_recommend'] = input('isRecommend'); //是否推荐商品
        $data['recommend_sort_order'] = input('recommendSortOrder'); //推荐商品排序
        $data['logistics_price'] = input('logisticsPrice');
        $data['carriage_type'] = input('carriageType');
        $data['carriage'] = input('carriage');
        $data['original_price'] = input('originalPrice');


    }
}