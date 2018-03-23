<?php

/**
 * Class Href
 * 页面跳转的方法
 * @author lxy<lxy_works@163.com>
 */

class Href
{
    /**
     * url跳转
     * @param $url
     */
    public static function URL($url)
    {
        Header("Location:" . $url);
        exit;
    }

    /**
     * 404跳转方法
     */
    public static function _404()
    {

        Header("HTTP/1.1 404 not found");
        Header("status: 404 not found");

        self::URL(C("url.404"));
    }

    /**
     * @param $url
     * 301跳转
     */
    public static function _301($url)
    {
        Header("HTTP/1.1 301 Moved Permanently");
        self::URL($url);
    }

}

