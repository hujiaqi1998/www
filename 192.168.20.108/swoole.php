<?php
/*Swoole\Timer::tick(2000, function(){
    echo "timeout\n";
});*/

// 创建swoole http服务器
// http://本机ip ： 9501
// swoole版本没有要求

// 在swoole事件中 echo 和 var_dump是输出在 控制台 不是浏览器
$http = new Swoole\Http\Server("0.0.0.0", 9501);

// 设置swoole进程个数
$http->set([
    'worker_num' => 1
]);
// 在创建的时候执行  ； 进程创建的时候触发时候
// 理解为一个构造函数，初始化
$http->on('workerStart', function ($server, $worker_id) {
    echo "理解为一个初始化操作即可\n";
});

// 通过浏览器访问 http://本机ip ：9501会执行的代码
$http->on('request', function ($request, $response) {
    // var_dump($request->get, $request->post);
    $response->header("Content-Type", "text/html; charset=utf-8");
    $response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
});

$http->start();
