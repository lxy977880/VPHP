<?php

/**
 * 数据库操作层
 * @author lxy<lxy_works@163.com>
 */
class DB
{
    // 读库的地址
    public static $dbRead;
    // 写库的地址
    public static $dbWrite;
    // 数据库名称
    public static $dbName;
    // 读数据库连接
    public static $linkR = 0;
    // 写数据库连接
    public static $linkW = 0;
    // 查询结果(读)
    public static $queryIdR = 0;
    // 查询结果(写)
    public static $queryIdW = 0;
    // 影响的行数
    public static $affectedRow;
    // 结果集中记录行数
    public static $rows;
    // 错误信息
    public static $error;


    /**
     * 数据库声明
     */
    static public function _connectR()
    {
        // 不存到读库连接就读取配置文件中的信息
        if (empty(self::$dbRead)) {
            self::$dbRead = C("server")['DBREAD'];
        }

        // 判断是否有链接属性
        if (0 == self::$linkR) {
            if (!is_array(self::$dbRead) || empty(self::$dbRead)) {
                Href::_404();
            }
            self::$linkR = @mysql_connect(self::$dbRead['HOST'] . ":" . self::$dbRead['PORT'], self::$dbRead['USER'], self::$dbRead['PASSWORD']);

            @mysql_query("set names utf8", self::$linkR);

            if (!@mysql_select_db(self::$dbRead['DATABASENAME'], self::$linkR)) {
                self::halt("不能打开指定的数据库:" . self::$dbRead['DATABASENAME']);
            }
        }
    }

    /**
     * 写库链接方法
     */
    static public function _connectW()
    {
        if (empty(self::$dbWrite)) {
            self::$dbWrite = C("server")['DBWRITE'];
        }

        if (0 == self::$linkW) {

            if (!is_array(self::$dbWrite) || empty(self::$dbWrite)) {
                Href::_404();
            }

            self::$linkW = @mysql_connect(self::$dbWrite['HOST'] . ":" . self::$dbWrite['PORT'], self::$dbWrite['USER'], self::$dbWrite['PASSWORD']);
            @mysql_query("set names utf8", self::$linkW);
            if (!self::$linkW) {
                self::halt("连接数据库(写)服务端失败!");
            }

            if (!@mysql_select_db(self::$dbWrite['DATABASENAME'], self::$linkW)) {
                self::halt("不能打开指定的数据库(写):" . self::$dbWrite['DATABASENAME']);
            }
        }

    }

    // 查询执行操作
    static public function select($queryString, $sign = "")
    {
        if (0 == self::$linkR) {
            self::_connectR();
        }
        // 判断查询操作中是否有其他规则
        if (!empty($sign)) {
            $queryString .= "#" . $sign;
        }
        // sql执行操作
        self::$queryIdR = @mysql_query($queryString, self::$linkR);

        // 判断是否执行成功
        if (!self::$queryIdR) {
            self::halt("SQL查询语句出错: " . $queryString);
        }
        // 定义数组接收
        $dataRows = array();
        // 当前查询有自定义规则
        /**
         * 备注：
         *   当sql查询的第二个参数中含有@rule: 的时候需要走自定义规则方式  比如字段中有id，title，keyword的时候，
         *   想到得到一个数组的key值是id，value值是title的可以使用这种定义规则, @rule:id->title,当想要将title
         *   和keyword都当做value值的时候 使用 @rule:id->title,keyword
         */
        if (strpos($sign, "@rule:") !== false) {
            // 去掉特殊标识符号
            $sign = str_replace("@rule:", "", $sign);
            if (strpos($sign, "->") !== false) {
                $fieldArr = explode("->", $sign);
                // 判断是需要将单个字段当做value值还是多个字段当做value值
                if (strpos($fieldArr[1], ",") !== false) {
                    $fieldArr2 = explode(",", $fieldArr[1]);
                    while ($dataRow = mysql_fetch_array(self::$queryIdR)) {
                        foreach ($fieldArr2 as $v) {
                            if (!empty($v)) {
                                $dataRows[$DataRow[$fieldArr[0]]][$v] = $dataRow[$v];
                            }
                        }
                    }
                } else {
                    while ($dataRow = mysql_fetch_array(self::$queryIdR)) {
                        $dataRows[$dataRow[$fieldArr[0]]] = $dataRow[$fieldArr[1]];
                    }
                }
            }
            return $dataRows;
        } else {
            // 判断是以什么形式展示出来结果  2array保留当前索引数组方式显示结果
            if (strtolower($sign) == "2array") {
                while ($dataRow = mysql_fetch_array(self::$queryIdR)) {
                    $dataRows[] = $dataRow;
                }
                return $dataRows;
            } else {
                // 保留关联数组方式显示结果
                while ($dataRow = mysql_fetch_assoc(self::$queryIdR)) {
                    $dataRows[] = $dataRow;
                }
                return $dataRows;
            }
        }
    }

    /**
     * 执行SQL
     */
    static public function query($queryString, $sign = "")
    {
        // 检测关键词，判断当前执行的是读操作还是写操作
        if (substr(trim($queryString), 0, 6) == "select") {
            // 当前执行的是读操作，直接实例化读库连接
            if (0 == self::$linkR) {
                self::_connectR();
            }
            // 返回数据
            return self::select($queryString, $sign);
        } else {
            // 当前执行的是写操作
            if (0 == self::$linkW) {
                self::_connectW();
            }

            self::$queryIdW = @mysql_query($queryString, self::$linkW);

            if (!self::$queryIdW) {
                return false;
            }
            // 返回执行操作结果
            return self::$queryIdW;
        }
    }

    /**
     * 执行查询(读) 返回资源类型数据
     */
    static public function getResourceData($queryString, $sign = "")
    {
        if (0 == self::$linkR) {
            self::_connectR();
        }
        if (!empty($sign)) {
            $queryString .= "#" . $sign;
        }
        self::$queryIdR = @mysql_query($queryString, self::$linkR);

        if (!self::$queryIdR) {
            self::halt("SQL查询语句出错: " . $queryString);
        }

        return self::$queryIdR;
    }

    /**
     * 执行SQL语句并返回由查询结果中第一行记录组成的数组
     */
    static public function getOneValue($sql, $field = "")
    {
        $result = self::getResourceData($sql);
        // 判断是否需要获取指定字段的值
        if (empty($field)) {
            $rows = @mysql_fetch_assoc($result);
            if (count($rows) == 1) {
                $value = "";
                foreach ($rows as $k => $v) {
                    $value = $v;
                    break;
                }

                return $value;
            } else {
                return $rows;
            }
        } else {
            $rows = @mysql_fetch_assoc($result);

            return $rows[$field];
        }
    }

    /**
     *  获取指定字段的值以数组当时返回
     */
    static public function getField2Array($sql, $field = "")
    {
        if (empty($field)) {
            return self::select($sql);
        }

        $result = self::getResourceData($sql);

        $array = array();

        while ($resultRow = mysql_fetch_assoc($result)) {
            $array[] = $resultRow[$field];
        }

        return $array;
    }

    /**
     * 获取上一步插入的id
     */
    static public function getInsertId()
    {
        return @mysql_insert_id(self::$linkIdW);
    }

    #返回结果集中记录行数
    static public function getNumRows()
    {
        self::$rows = mysql_num_rows(self::$linkR);

        return self::$rows;
    }

    #返回影响记录数
    static public function getAffectedRows()
    {
        self::$affectedRows = mysql_affected_rows(self::$queryIdR);

        return self::$affectedRows;
    }

    /**
     * 打印出错误信息
     */
    static public function halt($msg)
    {
        self::$error = mysql_error();

        self::$error .= $msg;

        return self::$error;
    }


    /**
     * 析构函数(关闭连接)
     */
    function __destruct()
    {
        @mysql_free_result(self::$linkR);

        @mysql_close(self::$queryIdR);

        @mysql_free_result(self::$linkW);

        @mysql_close(self::$queryIdW);
    }


}
