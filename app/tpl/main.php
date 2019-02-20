<?php

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
    <a href="../../index.html">返回首页</a>

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

if(file_put_contents(API_FILE_PATH .$type.'_'.$dbname.'.html', $html_tplt)){
    echo $dbname . ' Success!' . PHP_EOL;
}else {
    echo $dbname . ' Error!'. PHP_EOL;
}


