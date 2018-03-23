<?php

/**
 * Class Tools
 * 自动以工具类库
 * @author lxy<lxy_works@163.com>
 */
Class Tools
{

    /**
     * 获取ip归属地
     * @param $ip
     * @return array
     */
    public static function getIpAddress($ip)
    {
        $ip = preg_replace("/\s/", "", preg_replace("/\r\n/", "", $ip));

        $a = self::curl_url("http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=" . $ip . "&t=" . time());

        preg_match("/\"country\":\"(.*)\"/Uis", $a, $match1);

        preg_match("/\"province\":\"(.*)\"/Uis", $a, $match2);

        preg_match("/\"city\":\"(.*)\"/Uis", $a, $match3);

        return array(
            'country'  => self::ucode2zh($match1[1]),
            'province' => self::ucode2zh($match2[1]),
            'city'     => self::ucode2zh($match3[1])
        );
    }

    /**
     * @param $code   当前需转化的字符串信息
     * @return string
     * unicode编码转化为中文,转化失败返回原字符串
     */
    public static function ucode2zh($code)
    {
        $temp = explode('\u', $code);
        $rslt = array();
        array_shift($temp);
        foreach ($temp as $k => $v) {
            $v = hexdec($v);
            $rslt[] = '&#' . $v . ';';
        }

        $r = implode('', $rslt);

        return empty($r) ? $code : $r;
    }

    /**
     * 获取当前客户端的ip地址
     * @return array|false|int|string
     */
    public static function getIP()
    {
        if (getenv("HTTP_CLIENT_IP")) {
            $ip = getenv("HTTP_CLIENT_IP");
        } else if (getenv("HTTP_X_FORWARDED_FOR")) {

            $ip = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("REMOTE_ADDR")) {

            $ip = getenv("REMOTE_ADDR");
        } else {
            $ip = 0;
        }
        return $ip;
    }

    /**
     * 获取数组中指定key的信息
     * @param $arr
     * @param $key
     * @return array
     */
    public static function getArrayByKey($arr, $key)
    {
        $tmp_arr = array();
        foreach ($arr as $k => $v) {
            $id = $v[$key];
            if (!in_array($id, $tmp_arr)) {
                $tmp_arr[] = $id;
                $needArr[] = $arr[$k];
            }
        }
        return $needArr;
    }

    /**
     * 判断当前是PC端还是移动端
     * @return bool
     */
    public static function isPhone()
    {
        if (get('sign') == 'PC') {
            setcookie('ooopic_sign', get('sign'), time() + 3600, '/', ".ooopic.com");
            return false;
        }

        if (cookie('ooopic_sign') == 'PC') return false;

        if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        }
        if (isset ($_SERVER['HTTP_VIA'])) {
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }

        if (isset ($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile');
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }

        if (isset ($_SERVER['HTTP_ACCEPT'])) {
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
        return false;
    }

}