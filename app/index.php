<?php
/**
 * 数据字典生成脚本
 * User: Mark
 * Date: 2018/07/13
 * Time: 10:54
 */

header("Content-type: text/html; charset=utf-8");

function export_dict($dbname, $type, $config) {

    //************************************************mysql*************************************************************
    if($type == DATABASE_MYSQL) {
        $dsn = 'mysql:dbname='.$dbname.';host='.$config['host'];
        //数据库连接
        try {
            $con = new PDO($dsn, $config['user'], $config['password']);
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }

        $con->query("set names 'utf8'");

        //取得所有的表名
        $allTables = $con->query('SHOW tables')->fetchAll(PDO::FETCH_COLUMN);
        $allTables = '\'' .  str_replace(',', '\',\'', implode(',', $allTables)) . '\'';

        $sql = "SELECT t.*, c.*
            FROM INFORMATION_SCHEMA.COLUMNS AS c 
            INNER JOIN INFORMATION_SCHEMA.TABLES AS t ON t.table_schema = c.table_schema AND t.table_name = c.table_name
            WHERE t.TABLE_NAME IN ({$allTables})";

        $allField = $con->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $tablesInfo = array();
        foreach($allField as $k=>$v) {
            $tablesInfo[$v['TABLE_NAME']][] = $v;
        }
    }
    else
    {
    //************************************************pgsql*************************************************************
        try {
            //pg连接
            $dsn = "pgsql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";
            $con = new \PDO($dsn, $config['user'], $config['password']);
            $con->query("set client_encoding to 'utf8'");
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }

        $sql = "
            SELECT DISTINCT c.relname AS \"table_name\", a.attname AS \"field_name\",
             t.typname AS \"field_type\", a.attnotnull AS \"is_null\", b.description AS \"comment\",  a.attnum
            FROM pg_class c
                left JOIN pg_attribute a ON a.attrelid = c.oid 
                LEFT JOIN pg_description b ON a.attrelid = b.objoid aND a.attnum = b.objsubid
                INNER JOIN pg_type t ON a.atttypid = t.oid 
                LEFT JOIN pg_namespace n ON n.nspowner = c.relowner
            WHERE
                c.oid IN (
                    SELECT C.oid 
                    FROM pg_matviews v
                        INNER JOIN pg_class C ON v.matviewname = C.relname
                        LEFT JOIN pg_tablespace T ON v.TABLESPACE = T.spcname 
                    WHERE v.schemaname = '{$dbname}' 
                    
                    UNION
                        
                    SELECT c.oid
                    FROM pg_class
                        c LEFT JOIN pg_namespace n ON n.oid = c.relnamespace
                        LEFT JOIN pg_tablespace T ON T.oid = c.reltablespace
                        LEFT JOIN ( pg_inherits i INNER JOIN pg_class c2 ON i.inhparent = c2.oid LEFT JOIN pg_namespace n2 ON n2.oid = c2.relnamespace ) i2 ON i2.inhrelid = C.oid
                        LEFT JOIN pg_index ind ON ( ind.indrelid = c.oid ) 
                        AND ( ind.indisclustered = 't' )
                        LEFT JOIN pg_class ci ON ci.oid = ind.indexrelid
                        LEFT JOIN pg_foreign_table ft ON ft.ftrelid = c.oid
                        LEFT JOIN pg_foreign_server fs ON ft.ftserver = fs.oid 
                    WHERE
                    (
                        (
                            c.relkind = 'r' :: \"char\" 
                        ) 
                    OR ( 
                            c.relkind = 'f' :: \"char\" )
                        ) 
                        AND n.nspname = '{$dbname}' 
                    ) 
            AND a.attnum > 0 
            AND n.nspname = '{$dbname}' 
            AND c.relkind NOT IN('i')
            ORDER BY a.attnum ";

        $allField = $con->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $tablesInfo = array();
        foreach($allField as $k=>$v) {
            $tablesInfo[$v['table_name']][] = $v;
        }
    }


    if($type == DATABASE_MYSQL) {
        include 'tpl/mysql_table_content.php';
    } else {
        include 'tpl/pgsql_table_content.php';
    }

    include 'tpl/main.php';

}

include_once ('ini.config.php');

$page_link = '';
foreach ($databases as $type => $databaseArr) {
    $page_link .= "<h1/>{$type}</h1>" . PHP_EOL;
    foreach($databaseArr as $database){
        export_dict($database, $type, $config[$type]);
        $page_link .= "<h3><a href='" . API_FILE_PATH . $type.'_'.$database . ".html'>". $database . '</a></h3>' . PHP_EOL;
    }
    $page_link .= "<hr/>" . PHP_EOL;

}

include 'tpl/index_link.php';