<?php

/**
 * 包含文件的方法
 */
class AutoloadFile
{
    // 静态调用方法
    static public function run()
    {
        // 注册AUTOLOAD方法
        spl_autoload_register(array('AutoloadFile', 'autoload'));
    }


    // 自定义自动加载方法-只用于控制器中的继承
    static public function autoload($class)
    {
        // 定义加载控制器的方法
        if (strpos($class, 'Controller') !== false) {
            // 当前加载的是顶层控制器模块
            $fileName = ROOT_CODE . 'Controller/' . $class . CLASS_EXT;

            is_file($fileName) && include $fileName;
        } // 加载model类文件
        elseif (strpos($class, 'Model') !== false) {
            // 加载当前model类文件
            $fileName = ROOT_MODEL . $class . CLASS_EXT;

            is_file($fileName) && include $fileName;
        } // 加载sphinx,page,验证码扩展
        elseif (strpos($class, 'Page') !== false || strpos($class, 'VerifyCode') !== false || strpos($class, 'SphinxClient') !== false || strpos($class, 'Safe') !== false) {
            $fileName = VPHP_CLASS . $class . CLASS_EXT;
            is_file($fileName) && include $fileName;
        }
    }
}

