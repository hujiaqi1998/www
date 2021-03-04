<?php
require_once './redis.php';
require_once "./Input.php";
/*$config=[
    'master'=>[
        'host'=>'192.168.20.105',
        'port'=>6350
    ],
    'slaves'=>[
        'slave1'=>[
            'host'=>'192.168.20.105',
            'port'=>6330
        ],
        'slave0'=>[
            'host'=>'192.168.20.105',
            'port'=>6340
        ]
    ]
];*/
$config=[
    'master'=>[
        'host'=>'175.18.0.150',
        'port'=>6379
    ],
    'slaves'=>[
        'slave1'=>[
            'host'=>'175.18.0.130',
            'port'=>6379
        ],
        'slave0'=>[
            'host'=>'175.18.0.140',
            'port'=>6379
        ],
        'slave2'=>[
            'host'=>'175.18.0.120',
            'port'=>6379
        ],
    ]
];

// 在swoole事件中 echo 和 var_dump是输出在 控制台 不是浏览器
$http = new Swoole\Http\Server("0.0.0.0", 9500);

// 设置swoole进程个数
$http->set([
    'worker_num' => 1
]);
// 在创建的时候执行  ； 进程创建的时候触发时候
// 理解为一个构造函数，初始化
$http->on('workerStart', function ($server, $worker_id) use ($config){
    global $redis;
    $redis= new RedisMS($config);
});

// 通过浏览器访问 http://本机ip ：9501会执行的代码
$http->on('request', function ($request, $response) {
    global $redis;

    $response->end('OK');
});

$http->start();



/*$redisMS = new RedisMS();
$res = $redisMS->set('name', '好想舔洛天麒的大鸡巴!');
echo $redisMS->get('name');*/

