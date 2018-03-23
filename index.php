<?php
/**
 * 主入口文件
 */
// 检测php版本
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 定义根目录文件
define('ROOT', './');
define('APP_DEBUG', true);
// 引入入口文件
include './VPHP/VPHP.php';

