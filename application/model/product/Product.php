<?php
/**
 * Created by PhpStorm.
 * User: xubin
 * Date: 2020/7/7
 * Time: 11:03 PM
 */
namespace app\model\product;

use app\core\ApiModel;

class Product extends ApiModel{

    public function add($data){
        $sql = "insert into `product` (name, description, image, category_id, tag, quantity, original_price, price, is_discount, is_recommend, recommend_sort_order,
                                        logistics_price, carriage_type, carriage, sort_order, status, add_time)values (?,?,?,?,? ,?,?,?,?,? ,?,?,?,?,? ,?)";
        \think\Db::execute($sql,[ $data['name'], $data['description'], $data['image'], $data['category_id'], $data['tag'], $data['quantity'], $data['original_price'],
                                $data['price'], $data['is_discount'], $data['is_recommend'], $data['recommend_sort_order'], $data['logistics_price'],
                                $data['carriage_type'], $data['carriage'], $data['sort_order'], $data['status'], systemTime()

        ]);
        return \think\Db::getLastInsID();
    }
}