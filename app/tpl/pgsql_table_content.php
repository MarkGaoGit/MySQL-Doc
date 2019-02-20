<?php


$menu = $title = $contentTitle = '';
$title  = $dbname .' 数据字典';
$contentTitle .= '<h1 id="'. $dbname .'">' . $dbname .' 数据字典</h1>' . PHP_EOL;
$contentTitle .= '<blockquote>
                        <p>  本数据字典由PHP脚本自动导出,字典的备注来自数据库表及其字段的注释(<code>comment</code>).</p>
                   </blockquote>' . PHP_EOL;

//循环所有表
$tableHtml = '';

foreach ($tablesInfo as $tableName => $field) {

    $menu .= "<li> <a href='#". $tableName . "'>". $tableName ."</a></li>";

    $tableHtml .= '<h2 id="'. $tableName. '">'. $tableName .'</h2>';
    $tableHtml .=  '<table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                            <th>字段名</th>
                            <th>数据类型</th>
                            <th>允许NULL</th>
                            <th>备注</th>
                            </tr>
                        </thead>
                        <tbody> ' . PHP_EOL;

    foreach($field as $v) {
        $isNull = $v['is_null'] == 't' ? '否' : '是';
        $tableHtml .= "<tr>
                    <td>{$v['field_name']}</td> 
                    <td>{$v['field_type']}</td> 
                    <td>{$isNull}</td>
                    <td>{$v['comment']}</td> 
                   </tr>" . PHP_EOL;
    }
    $tableHtml .= ' </tbody> </table>' . PHP_EOL;
}
