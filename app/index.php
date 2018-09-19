<?php
/**
 * 数据字典生成脚本
 * User: Mark
 * Date: 2018/07/13
 * Time: 10:54
 */

header("Content-type: text/html; charset=utf-8");

function export_dict($dbname, $config) {

    $dsn = 'mysql:dbname='.$dbname.';host='.$config['host'];
    //数据库连接
    try {
        $con = new PDO($dsn, $config['user'], $config['password']);
    } catch (PDOException $e) {
        die('Connection failed: ' . $e->getMessage());
    }

    $con->query("set names 'utf8'");

    $tables = $con->query('SHOW tables')->fetchAll(PDO::FETCH_COLUMN);

    //取得所有的表名
    $_tables = array();
    foreach ($tables as $table) {
        $_tables[]['TABLE_NAME'] = $table;
    }

    //循环取得所有表的备注及表中列消息
    foreach ($_tables as $k => $v) {

        $sql = 'SELECT * FROM ';
        $sql .= 'INFORMATION_SCHEMA.TABLES ';
        $sql .= 'WHERE ';
        $sql .= "table_name = '{$v['TABLE_NAME']}' AND table_schema = '{$dbname}'";
        $tr = $con->query($sql)->fetch(PDO::FETCH_ASSOC);
        $_tables[$k]['TABLE_COMMENT'] = $tr['TABLE_COMMENT'];
        $_tables[$k]['ENGINE'] = $tr['ENGINE'];
        $_tables[$k]['CREATE_TIME'] = strtotime($tr['CREATE_TIME']);

        $sql = 'SELECT * FROM ';
        $sql .= 'INFORMATION_SCHEMA.COLUMNS ';
        $sql .= 'WHERE ';
        $sql .= "table_name = '{$v['TABLE_NAME']}' AND table_schema = '{$dbname}'";
        $fields = [];
        $field_result = $con->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        foreach ($field_result as $fr)
        {
            $fields[] = $fr;
        }
        $_tables[$k]['COLUMN'] = $fields;

    }
    unset($con);
    $_tables = array_combine( array_column($_tables, 'CREATE_TIME'), $_tables);
    ksort($_tables);

    $menu = $title = $contentTitle = '';
    $title  = $dbname .' 数据字典';
    $contentTitle .= '<h1 id="'. $dbname .'">' . $dbname .' 数据字典</h1>' . PHP_EOL;
    $contentTitle .= '<blockquote>
                        <p>  本数据字典由PHP脚本自动导出,字典的备注来自数据库表及其字段的注释(<code>comment</code>).</p>
                        <p>  索引类型：PRI 主键索引    UNI 唯一索引    NUL 普通索引</p>
                   </blockquote>' . PHP_EOL;

    //循环所有表
    $tableHtml = '';
    foreach ($_tables as $k => $v) {

        $menu .= "<li> <a href='#". $v['TABLE_NAME'] . $v['TABLE_COMMENT'] . "'>";
        $menu .= $v['TABLE_NAME'] . $v['TABLE_COMMENT'];
        $menu .= "</a></li>";

        $tableHtml .= '<h2 id="'. $v['TABLE_NAME'].$v['TABLE_COMMENT'] .'">'. $v['TABLE_NAME'].$v['TABLE_COMMENT'] .'</h2>';
        $tableHtml .= '<h3>表引擎:'. $v['ENGINE'].'</h3>';
        $tableHtml .=  '<table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                            <th>字段名</th>
                            <th>数据类型</th>
                            <th>默认值</th>
                            <th>允许非空</th>
                            <th>索引类型</th>
                            <th>自动递增</th>
                            <th>备注</th>
                            </tr>
                        </thead>
                        <tbody> ' . PHP_EOL;
        foreach ($v['COLUMN'] as $f) {
            $COLUMN_DEFAULT = $f['COLUMN_DEFAULT'] == '' ? " &#39; &#39;" : $f['COLUMN_DEFAULT'];
            $COLUMN_KEY = empty($f['COLUMN_KEY']) ? '-' : $f['COLUMN_KEY'];
            $EXTRA = $f['EXTRA'] == 'auto_increment' ? '是' : '-';
            $COLUMN_COMMENT = empty($f['COLUMN_COMMENT']) ? '-' : str_replace('|', '/', $f['COLUMN_COMMENT']);

            $tableHtml .= "<tr>
                    <td>{$f['COLUMN_NAME']}</td> 
                    <td>{$f['COLUMN_TYPE']}</td> 
                    <td>{$COLUMN_DEFAULT}</td>
                    <td>{$f['IS_NULLABLE']}</td> 
                    <td>{$COLUMN_KEY}</td> 
                    <td>{$EXTRA}</td>
                    <td>{$COLUMN_COMMENT}</td>
                   </tr>" . PHP_EOL;
        }
        $tableHtml .= ' </tbody> </table>' . PHP_EOL;

    }
    //html输出
    $html_tplt = <<<EOT
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>{$title}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" type="text/css" href="../resources/body-content.css">
    <script type="text/javascript" src="../resources/jquery-2.1.4.js"></script>
</head>
<body>
<div style="margin:auto; width:1300px;">
<div class="body-content" id="content" style="margin:auto; width: 980px; float:right;">
{$contentTitle}
{$tableHtml}
</div>
<div id="menu" style="width:270px; float:left; position:fixed; left:4%; max-height:400px; ">
    <h2>Menu</h2>
        <input type="text" id="keyword" placeholder="输入表名模糊查询">
    <ul>
        {$menu}
    </ul>
</div>
<div style="clear:both;"></div>
</div>
<script >
    $('ul li').on('mouseover',function(){
        $(this).children('a').addClass('hover').siblings().removeClass('hover');
    });
    $('ul li').on('mouseout',function(){
        $(this).children('a').removeClass('hover');
    });
    $('ul li').on('click',function(){
        $(this).addClass('active').siblings().removeClass('active');
    });
    $("input[type='text']").on('input', function() {
        var keyword = $(this).val();
        $('ul li').hide().filter(':contains('+keyword+')').show();
    });
</script>

</body>
</html>
EOT;

    if(!is_dir(API_FILE_PATH)) {
        mkdir(API_FILE_PATH,0777,true);
    }

    if(file_put_contents(API_FILE_PATH .$dbname.'.html', $html_tplt)){
        echo $dbname . ' Success!' . PHP_EOL;
    }else {
        echo $dbname . ' Error!'. PHP_EOL;
    }

}

include_once ('ini.config.php');

$page_link = '';
foreach ($databases as $database) {
    export_dict($database, $config);
    $page_link .= "<h3><a href='" . API_FILE_PATH . $database . ".html'>". $database . ' 数据字典</a></h3>' . PHP_EOL;
}

// 输出连接页面
$index_tpl = <<<EOT
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>数据字典</title>
</head>
<style>
    * { padding:0; margin:0;}
    div { width:1000px; margin:30px auto; text-align: center; cursor:pointer; }
    div a { text-decoration: none; color:#000; }
    h3 { line-height: 40px; }
</style>
<body>
    <div>
        {$page_link}
    </div>
</body>
</html>
EOT;

if(file_put_contents('./index.html', $index_tpl)){
    echo 'Index Success!' . PHP_EOL;
}else {
    echo 'Index Error!'. PHP_EOL;
}