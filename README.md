个人已定义框架-基于Smarty模板引擎编写

********************************
首先介绍一下框架的核心包内容
VPHP 是一个为了满足特定业务需求所编写的一个框架

框架核心位于VPHP里面  下面是文件目录与说明

_init   自定义预加载模板文件
    --- Config  配置文件
        --- condigure.php  配置文件书写地方，在这里面可以写数据库配置，reids配置，memcache配置，域名配置，全局常量配置
    --- Log   日志存储位置
    --- Model model文件存放位置 数据交互都在这里面
        --- Model.class.php model类包的顶层类
    --- Source 控制器层
        --- Ajax ajax控制处理层
            --- IndexAjax.class.php  ajax控制层方法
        --- Controller 业务控制处理层
            --- IndexController.class.php 
    ---- View 视图层
        --- index
            --- index.html 视图模板处理层
上面这些是为了预先引入文件方法，减少不必要的工作量

Common  核心类包层
    --- Class 基础class类
        --- Encrypt.class.php 数据安全获取的方法[包括字符串过滤与密码加密方法]
        --- Href.class.php      URL跳转方法
        --- Page.class.php      完美分页方法
        --- Safe.class.php      安全过滤方法
        --- String.class.php    字符串处理方法
        --- Tools.class.php     基础工具类
        --- Translate.class.php 拼音转化方法
    --- Drive 驱动层
        --- Cache.drive.php 缓存类库，基于阿里云的OCS
        --- DB.drive.php    数据库操作方法，自动以一些基础的方法，可执行读写分离操作
        --- SphinxApi.drive.php sphinx核心类
        --- UpYun.drive.php  又拍云处理类
        --- View.drive.php  Smarty模板引用处理
    --- Function  公共方法加载
        --- autoload.function.php 自定义自动加载方法
        --- common.function.php   全局自定义方法使用规则
    --- Model 顶层model方法 
    --- Plus  安全过滤方法，防止sql注入一级xss共计

Tpl_Drive Smarty 模板驱动类包

VPHP.php  php入口主文件   里面包含文件生成，类包引入，变量声明等功能



index.php 主入库 基础内容如下

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

