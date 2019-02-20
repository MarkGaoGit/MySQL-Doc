<?php
/**
 * 数据字典脚本配置
 * User: Mark
 * Date: 2018/07/13
 * Time: 10:54
 */
//文档路径
define('API_FILE_PATH', './docs/');

if (strtolower(substr(php_sapi_name(), 0, 3)) == 'cli') {
    define('ROOT_PATH', './');
} else {
    define('ROOT_PATH', '../');
}

define('DATABASE_MYSQL', 'mysql');
define('DATABASE_PGSQL', 'pgsql');

//数据库配置
$config = array(
    DATABASE_MYSQL => array(
        'host'     => 'xxx',
        'user'     => 'xxx',
        'password' => 'xxx',
    ),
    DATABASE_PGSQL => array(
        'host'     => 'xxx',
        'port'     => 5432,
        'dbname'    => 'xxx',
        'user'     => 'xxx',
        'password' => 'xxx',
    )
);

//表
$databases = array(
    DATABASE_MYSQL => array(
        'xxx',

    ),
    DATABASE_PGSQL => array(
        'schema'
    )
);
