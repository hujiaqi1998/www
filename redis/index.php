<?php
/*include './redis.php';*/

$redis= new Redis();
$redis->connect('192.168.20.103',6379);//serverip port
echo $redis ->get("name");

echo '<h1>Hi</h1>';

/*$redisMS = new RedisMS();
$res = $redisMS->set('name', '好想舔洛天麒的大鸡巴!');
echo $redisMS->get('name');*/

