<?php

/**
 * Class Safe
 * @author lxy<lxy_works@163.com>
 * 安全过滤的方法
 */

class Safe
{
    /**
     * 过滤$_GET，$_POST，$_COOKIE中的特殊字符，防止SQL注入攻击
     * @param $string
     * @return array|string
     */
    public static function rDaddslashes($string)
    {
        if (is_array($string)) {
            foreach ($string as $key => $val) {
                $string[$key] = daddslashes($val, $force);
            }
        } else {
            $string = addslashes($string);
        }
        return $string;
    }



}
