<?php
/**
 * 系统配置文件 
 */	
return [
	"domain" => [
		// 主域名
		"mainhost"   => "http://www.vphp.com",
	],
	"url"    => [

	],
	"global" => [

	],
	"server" => [
		// 读库连接位置
		"DBREAD" => array(
						"HOST"         => "xxxxxxxxxxxxxxxxx",
						"PORT"         => "3306",
						"USER"         => "xxxxxxxx",
						"PASSWORD"     => "xxxxxxxx",
						"DATABASENAME" => "xxxxxxxx"
						),
		// 写库连接位置
		"DBWRITE" => array(
						"HOST"         => "xxxxxxxxxxxxxxxxx",
						"PORT"         => "3306",
						"USER"         => "xxxxxxxx",
						"PASSWORD"     => "xxxxxxxx",
						"DATABASENAME" => "xxxxxxxx"
						),
	],
];