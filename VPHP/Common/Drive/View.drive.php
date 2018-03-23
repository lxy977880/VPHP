<?php
/**
 *
 */
class View
{
    protected $_COMMON;

    public $Smarty;

    protected $page_code = 0;

    protected $exetime = 0;

    function __construct()
    {
        // 引入smarty模板引擎
        include(VPHP_TPL_DRIVE . 'Smarty.class.php');

        $this->Smarty = new Smarty;
        $this->Smarty->caching          = false;                         // 缓存状态
        $this->Smarty->compile_check    = true;                    //
        $this->Smarty->debugging        = false;                       // debug状态
        $this->Smarty->template_dir     = ROOT_VIEW;                // 视图层文件存到目录
        $this->Smarty->config_dir       = VPHP_TPL_DRIVE . 'config';  // 配置层文件存放目录
        $this->Smarty->compile_dir      = ROOT_RUNCACHE;             // 缓存文件顶级目录
        $this->Smarty->cache_dir        = ROOT_RUNCACHE . 'cache';     // 缓存文件存放目录
        $this->Smarty->left_delimiter   = '<%{';                  // 左边界符
        $this->Smarty->right_delimiter  = '}%>';                 // 有边界符

        $View_Config_List = array("domain", "url", "global");

        $Config = C();

        foreach ($View_Config_List as $k => $v) {
            $View_Config[$v] = $Config[$v];
        }

        $this->Smarty->assign("Config", $View_Config);

        $this->_data($this->_COMMON);

    }

    function _data($key, $value = "")
    {
        if (empty($value)) {
            $this->Smarty->assign($key);
        } else {
            $this->Smarty->assign($key, $value);
        }

    }

    function _view($tpl = "")
    {
        $tpl = strtolower($tpl);

        if (strpos($tpl, "/") !== false) {
            $TplPath = ROOT_VIEW;

            $arr = explode("/", $tpl);

            foreach ($arr as $v) {
                $TplPath .= "/" . $v;
            }

            $TplPath .= ".html";

        } elseif (strpos($tpl, ".") !== false) {

            $TplPath = ROOT_VIEW;

            $arr = explode(".", $tpl);

            foreach ($arr as $v) {
                $TplPath .= "/" . $v;
            }

            $TplPath .= ".html";

        } else {
            $PageTplsDir = strtolower(substr(get_class($this), 0, -4));

            $TplPath = ($tpl) ? ROOT_VIEW . $PageTplsDir . "/" . $tpl . ".html" : ROOT_VIEW . $PageTplsDir . "/" . $PageTplsDir . ".html";
        }


        if (!file_exists($TplPath)) {
            echo "view not found!!";
            exit;
        }

        $this->Smarty->display($TplPath);
    }

    function __destruct()
    {

    }

}

