<?php
/**
 * 系统全局定义公共方法的
 */

/**
 * 字符串处理方法
 * @param $str
 * @return string
 */
function _action($str)
{
    $strResult = $str;
    $strResult = strip_tags($strResult);//去掉html标签
    if (!get_magic_quotes_gpc())//判断设置是否开启 只有0和1两种情况
    {
        $strResult = addslashes($strResult);//转换sql语句特殊字符
    }
    return $strResult;
}

/**
 * 定义get方法
 * @param $data
 * @return bool|int|mixed|null|string
 */
function get($data)
{
    return isset($_GET[$data]) ? _action($_GET[$data]) : "";
}

/**
 * 定义request方法
 * @param $data
 * @return string
 */
function request($data)
{
    return isset($_REQUEST[$data]) ? _action($_REQUEST[$data]) : "";
}

/**
 * 定义post方法
 * @param $data
 * @return bool|int|mixed|null|string
 */
function post($data)
{
    return isset($_POST[$data]) ? _action($_POST[$data]) : "";
}

/**
 * 定义cookie设置方法
 * @param $key
 * @param string $data
 * @param int $time
 * @return bool|int|mixed|null|string
 */
function cookie($key, $data = "", $time = 3600)
{
    if (empty($data)) {
        return isset($_COOKIE[$key]) ? f($_COOKIE[$key]) : "";
    } else {

        $domain_cookie = C("domain.cookie");

        if (empty($domain_cookie)) {
            echo "function-cookie::no Config->domain->cookie";
            exit;
        }

        setcookie($key, $data, time() + $time, "/", $domain_cookie);
    }
}

/**
 * 定义返回上一级页面的方法
 * @param int $time
 */
function backUrl($time = 3600)
{
    $return_url = 'http://' . $_SERVER ['SERVER_NAME'] . $_SERVER ['REQUEST_URI'];
    cookie("backUrl", $return_url, 3600);
}

/**
 * 删除cookie的方法
 * @param $key
 */
function delCookie($key)
{
    if (strpos($key, ",") !== false) {
        $keys = explode(",", $key);

        foreach ($keys as $v) {
            cookie($v, "null", -1);
        }
    } else {
        cookie($key, "null", -1);
    }

}

/**
 * 获取url参数
 * @param $data
 * @return bool|int|mixed|null|string
 */
function getUrlData($data)
{
    $data = get($data);

    if (preg_match("/^[\w]+$/Uis", $data)) {
        return $data;
    } else {
        return "";
    }
}

/**
 * 关键词特殊字符过滤方法
 * @param $kw
 * @return bool|int|mixed|null|string
 */
function FKW($kw)
{
    $kw = _action($kw);

    if (!empty($kw)) {
        $kw = preg_replace('/\s+/i', ' ', $kw);
        $kw = preg_replace('/　/i', ' ', $kw);
        $kw = preg_replace('/\//i', '', $kw);
        $kw = mb_ereg_replace('^(　| )+', '', $kw);
        $kw = mb_ereg_replace('(　| )+$', '', $kw);
    }

    return $kw;
}

/**
 * 路径特殊字符过滤方法
 * @param $str
 * @return mixed
 */
function pathFilter($str)
{
    return preg_replace('/[\/|\\|.]/', '', $str);
}


/**
 * 检测字符串是否是UTF8格式
 * @param  String $str 被检测字符串
 * @return boolean true为是，false为否
 */
function isUTF8($str)
{
    return ($str === mb_convert_encoding(mb_convert_encoding($str, "UTF-32", "UTF-8"), "UTF-8", "UTF-32"));
}


/**
 *打印复杂数据类型
 */

/**
 * 打印数据的方法
 * @param $arr          数据
 * @param int $isexit   是否die掉
 */
function dd($arr, $isDie = 0)
{
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
    if ($isDie) die;
}

/**
 *执行时间打印
 */

/**
 * 微妙级获取时间方法
 * @param $sign
 * @param string $start
 * @return float
 */
function exeTime($sign, $start = "")
{
    global $$sign, $$start;

    $$sign = microtime(true);

    if (!empty($start)) {
        return round($$sign - $$start, 4);
    }
}

/**
 * 初始化数组索引,将数组从新排序存放到新的索引数组中
 * @param $arr
 * @param bool $r
 * @return array
 */
function clearIndex($arr, $userY = true)
{
    $brr = array();

    foreach ($arr as $v) {
        $brr[] = $v;
    }

    return $brr;
}

/**
 * 获取config内容方法
 * @param string $str
 * @return mixed
 */
function C($str = "")
{
    $value = $Config = unserialize(CONFIG);

    if ($str) {
        $str = strtolower($str);

        $strArr = explode(".", $str);

        foreach ($strArr as $v) {
            $value = $value[$v];
        }
    }

    return $value;
}

/**
 * 文件导入方法
 * @param $area
 * @param $filelist
 * @param string $ext
 */
function import($area, $filelist, $ext = "")
{
    $area = strtolower($area);

    $path = "";

    if (substr($area, 0, 1) == "@") {
        $area = substr($area, 1);
        switch ($area) {
            case "drive":
                $path = VPHP_DRIVE;
                $ext = ".drive.php";
                break;
            case "class":
                $path = VPHP_CLASS;
                $ext = ".class.php";
                break;
            case "function":
                $path = VPHP_FUNCTION;
                $ext = ".function.php";
                break;
            case "model":
                $path = VPHP_MODEL;
                $ext = ".class.php";
                break;
            case "plus":
                $path = VPHP_PLUS;
                break;
            default:
                echo "import error @area!!";
                exit;
        }
    } else {
        switch ($area) {
            case "model":
                $path = ROOT_MODEL;
                $ext = "Model.class.php";
                break;
            default:
                echo "import error area!!";
                exit;
        }
    }

    $fileArray = explode(",", $filelist);
    
    foreach ($fileArray as $v) {
        include($path . $v . $ext);
    }

}

/**
 * 日志写入方法
 * @param $file
 * @param $data
 * @param int $append
 */
function wlog($file, $data, $append = 1)
{
    if ($append == 1) {
        file_put_contents($file, $data, FILE_APPEND);
    } else {
        file_put_contents($file, $data);
    }
}

/**
 * @param $string
 * @param $rule
 * @return string
 */
function vphp_iconv($string, $rule)
{
    if (strpos($rule, "->") === false) {
        echo "vphp_iconv::rule is error 1";
        exit;
    }

    $arr = explode("->", $rule);

    if (count($arr) != 2 || empty($arr[0]) || empty($arr[1])) {
        echo "vphp_iconv::rule is error 2";
        exit;
    }

    return iconv($arr[0], $arr[1], $string);
}
