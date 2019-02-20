<?php


$menu = $title = $contentTitle = '';
$title  = $dbname .' 数据字典';
$contentTitle .= '<h1 id="'. $dbname .'">' . $dbname .' 数据字典</h1>' . PHP_EOL;
$contentTitle .= '<blockquote>
                        <p>  本数据字典由PHP脚本自动导出,字典的备注来自数据库表及其字段的注释(<code>comment</code>).</p>
                        <p>  索引类型：PRI 主键索引    UNI 唯一索引    NUL 普通索引</p>
                   </blockquote>' . PHP_EOL;

//循环所有表
$tableHtml = '';

foreach ($tablesInfo as $tableName => $field) {

    $menu .= "<li> <a href='#". $tableName . $field[0]['TABLE_COMMENT'] . "'>";
    $menu .= $tableName . $field[0]['TABLE_COMMENT'];
    $menu .= "</a></li>";

    $tableHtml .= '<h2 id="'. $tableName.$field[0]['TABLE_COMMENT'] .'">'. $tableName.$field[0]['TABLE_COMMENT'] .'</h2>';
    $tableHtml .= '<h3>表引擎:'. $field[0]['ENGINE'].'</h3>';
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

    foreach($field as $v) {
        $COLUMN_DEFAULT = $v['COLUMN_DEFAULT'] == '' ? " &#39; &#39;" : $v['COLUMN_DEFAULT'];
        $COLUMN_KEY = empty($v['COLUMN_KEY']) ? '-' : $v['COLUMN_KEY'];
        $EXTRA = $v['EXTRA'] == 'auto_increment' ? '是' : '-';
        $COLUMN_COMMENT = empty($v['COLUMN_COMMENT']) ? '-' : str_replace('|', '/', $v['COLUMN_COMMENT']);

        $tableHtml .= "<tr>
                    <td>{$v['COLUMN_NAME']}</td> 
                    <td>{$v['COLUMN_TYPE']}</td> 
                    <td>{$COLUMN_DEFAULT}</td>
                    <td>{$v['IS_NULLABLE']}</td> 
                    <td>{$COLUMN_KEY}</td> 
                    <td>{$EXTRA}</td>
                    <td>{$COLUMN_COMMENT}</td>
                   </tr>" . PHP_EOL;
    }
    $tableHtml .= ' </tbody> </table>' . PHP_EOL;

}