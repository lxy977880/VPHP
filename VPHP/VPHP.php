<?php
// 定义时区
date_default_timezone_set("Asia/Shanghai");
// 定义根目录文件
define("VPHP", dirname(__FILE__) . "/");
// 定义class类名后缀
define("CLASS_EXT", ".class.php");
// 定义root目录
defined("ROOT") or die("ERROR::没有指定网站根目录_ROOT");

// 判断当前是否开启报错信息
if (APP_DEBUG) {
    ini_set('display_errors',1);            //错误信息
    ini_set('display_startup_errors',1);    //php启动错误信息
    error_reporting(E_ALL &~ E_NOTICE);     //打印出所有的 错误信息(除了notice)
    ini_set('error_log', dirname(__FILE__) . 'log/'.date('Y-m-d', time()).'error_log.txt');
}

#检测是否安装了VPHP,如果没有安装执行安装程序(暂时省略)===start
if (!file_exists(ROOT . "Source")) {
    // 安装源文件-相当于Controller
    mkdir(ROOT . "Source", 0777);
    mkdir(ROOT . "Source/Controller", 0777);
    copy(VPHP . "_init/Source/index.html", ROOT . "Source/index.html");
    copy(VPHP . "_init/Source/Controller/index.html", ROOT . "Source/Controller/index.html");
    copy(VPHP . "_init/Source/Controller/IndexController.class.php", ROOT . "Source/Controller/IndexController.class.php");
    // 安装Ajax配置文件-用于一些ajax操作是文件存放位置
    mkdir(ROOT . "Source/Ajax", 0777);
    copy(VPHP . "_init/Source/Ajax/index.html", ROOT . "Source/Ajax/index.html");
    copy(VPHP . "_init/Source/Ajax/IndexController.class.php", ROOT . "Source/Ajax/IndexController.class.php");
    // 安装系统配置文件
    mkdir(ROOT . "Config", 0777);
    copy(VPHP . "_init/Config/index.html", ROOT . "Config/index.html");
    copy(VPHP . "_init/Config/configure.php", ROOT . "Config/configure.php");
    // 安装Model类文件
    mkdir(ROOT . "Model", 0777);
    copy(VPHP . "_init/Model/index.html", ROOT . "Model/index.html");
    copy(VPHP . "_init/Model/Model.class.php", ROOT . "Model/Model.class.php");
    // 安装视图模块文件
    mkdir(ROOT . "View", 0777);
    copy(VPHP . "_init/View/index.html", ROOT . "View/index.html");
    mkdir(ROOT . "View/index", 0777);
    copy(VPHP . "_init/View/index/index.html", ROOT . "View/index/index.html");
    // 安装日志记载文件
    mkdir(ROOT . "Log", 0777);
    copy(VPHP . "_init/Log/index.html", ROOT . "Log/index.html");
    // 安装缓存存放文件
    mkdir(ROOT . "Runcache", 0777);
}
#检测是否安装了VPHP,如果没有安装执行安装程序(暂时省略)===end

#定义系统常量
define("VPHP_COMMON", VPHP . "Common/");
define("VPHP_DRIVE", VPHP_COMMON . "Drive/");
define("VPHP_FUNCTION", VPHP_COMMON . "Function/");
define("VPHP_CLASS", VPHP_COMMON . "Class/");
define("VPHP_MODEL", VPHP_COMMON . "Model/");
define("VPHP_PLUS", VPHP_COMMON . "Plus/");
define("VPHP_TPL_DRIVE", VPHP . "Tpl_Drive/");

#定义项目常量
define("ROOT_CODE", ROOT . "/Source/");
define("ROOT_CONFIG", ROOT . "Config/");
define("ROOT_MODEL", ROOT . "Model/");
define("ROOT_VIEW", ROOT . "View/");
define("ROOT_LOG", ROOT . "Log/");
define("ROOT_RUNCACHE", ROOT . "Runcache/");

#加载公共文件
include(VPHP_FUNCTION . "common.function.php");
include(VPHP_FUNCTION . "autoload.function.php");
#使用自动加载类
AutoloadFile::run();
#配置configure
$Config = include(ROOT_CONFIG . "configure.php");
define("CONFIG", serialize($Config));

#配置cookie变量
//define("COOKIE",serialize($_COOKIE));

#加载底层的驱动
import("@drive", "View,DB,Cache");
#加载底层的Class工具
import("@class", "Href,Tools,Encrypt");
#加载Model(里面填写多数情况下多有的方法)
import("@model", "Model");
#导入安全过滤
import("@plus", "wsf.php");

#处理URL参数
$m      = getUrlData('m');      unset($_GET['m']);
$a      = getUrlData('a');      unset($_GET['a']);
$ajax   = getUrlData('ajax');   unset($_GET['ajax']);

$m      = $m ? $m : "index";
$a      = $a ? $a : "index";

#根据文件的用途划分存储区域(可自定义)
if ($ajax) {
    $PHP_DIR = ROOT_CODE . "/Ajax/";
    $ObjectName = strtoupper(substr($m, 0, 1)) . substr($m, 1) . "Ajax";
} else {
    $PHP_DIR = ROOT_CODE . "/Controller/";
    $ObjectName = strtoupper(substr($m, 0, 1)) . substr($m, 1) . "Controller";
}

$ObjectPath = $PHP_DIR . $ObjectName . CLASS_EXT;

if (file_exists($ObjectPath)) {
    include($ObjectPath);

    if (class_exists($ObjectName)) {
        $Object = new $ObjectName();

        if (method_exists($Object, $a)) {
            $Object->$a();
            exit;
        } else {
            if ($Config['debug']) {
                echo "ERROR::行为【" . $a . "】不存在！";
                exit;
            } else {
                Href::_404();
            }
        }
    } else {

        if ($Config['debug']) {
            echo "ERROR::页面【" . $m . "】不存在！";
            exit;

        } else {
            Href::_404();
        }
    }
} else {
    Href::_404();
}
