<?php
/**
 * Created by PhpStorm.
 * User: xubin
 * Date: 2019/2/11
 * Time: 4:06 PM
 */
namespace app\model\member;
class User extends \app\core\ApiModel {

    /**
     * 账号列表
     * @param $master
     * @param $store_id
     * @param int $page
     * @param int $pageSize
     * @param string $username
     * @return array|mixed
     */
    public function getLists($master,$page = 0, $pageSize = 10,$username = '',$teamId = '') {

    }
}