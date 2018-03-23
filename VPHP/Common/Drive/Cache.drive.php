<?php

/**
 * 缓存类
 * 该类基于阿里云的memcache缓存书写的
 */
class Cache
{
    static $aliyOCS;

    static public function _connect()
    {
        if (empty(self::$aliyOCS)) {
            $OCS_HOST = unserialize(OCS_HOST);

            self::$aliyOCS = new Memcached;
            self::$aliyOCS->setOption(Memcached::OPT_BINARY_PROTOCOL, true); //使用binary二进制协议
            self::$aliyOCS->setOption(Memcached::OPT_TCP_NODELAY, true);    // 开启已连接socket的无延迟特性
            self::$aliyOCS->addServer($OCS_HOST['HOST'], $OCS_HOST['PORT']);
        }
    }

    /**
     * 存储一个元素
     * @param String $key 元素的键
     * @param String $val 元素的值
     * @param Intval $time 存储元素的时间
     */
    static public function set($key, $val = "", $time = 3600)
    {
        self::_connect();

        return (is_array($key)) ? self::$aliyOCS->setMulti($key,$time) : self::$aliyOCS->set($key, $val, $time);

        return false;
    }

    /**
     * 获取元素
     * @param  String $key 元素的键
     */
    static public function get($key)
    {
        self::_connect();

        return (is_array($key)) ? self::$aliyOCS->getMulti($key) : self::$aliyOCS->get($key);

        return false;
    }

}