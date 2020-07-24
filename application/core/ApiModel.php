<?php

namespace app\core;

use think\Model;

/**
 * API
 *
 * @author aoxiang
 */
class ApiModel extends Model {

    // 获取单条
    public static function find($sql, $param = []) {
        if ($param) {
            $data = \think\Db::query($sql, $param);
        } else {
            $data = \think\Db::query($sql);
        }
        return isset($data[0]) ? $data[0] : [];
    }

    // 获取多条
    public static function findAll($sql, $param = []) {
        if ($param) {
            $data = \think\Db::query($sql, $param);
        } else {
            $data = \think\Db::query($sql);
        }
        return $data;
    }

    // 分页查询模板
    public static function _getList($_sql, $_sql_count, $where = NULL, $order = NULL, $is_paging = FALSE, $page = 0, $limit = 20) {
        $data = $is_paging ? ['count' => 0, 'list' => []] : [];
        $sql = $_sql;
        $sql_limit = '';
        $sql_where = '';
        $sql_order = '';

        if ($where) {
            $sql_where = ' where ' . implode(' and ', $where);
        }
        if ($order) {
            $sql_order = ' order by ' . implode(' , ', $order);
        }
        if ($is_paging) {
            $sql_count = $_sql_count;
            $start = $page * $limit;
            $sql_limit = " limit {$start}, {$limit}";
            $_count = self::find($sql_count . $sql_where );
            if (!$_count['count']) {
                return $data;
            }
            $data['count'] = $_count['count'];
            //总页数判断
            if ($start >= $data['count']) {
                return $data;
            }
        }
        $_data = self::findAll($sql . $sql_where . $sql_order . $sql_limit);
        if ($is_paging) {
            $data['list'] = $_data;
        } else {
            $data = $_data;
        }
        return $data;
    }

}
