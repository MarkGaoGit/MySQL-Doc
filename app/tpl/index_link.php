<?php

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
    div { width:400px; margin:30px auto; cursor:pointer; }
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

if(file_put_contents(ROOT_PATH .'/index.html', $index_tpl)){
    echo 'Index Success!' . PHP_EOL;
}else {
    echo 'Index Error!'. PHP_EOL;
}