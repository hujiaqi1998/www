<?php
class RedisMS{
    private $config=[
        'master'=>[
            'host'=>'192.168.20.103',
            'port'=>6350
        ],
        'slaves'=>[
            'slave_130'=>[
                'host'=>'192.168.20.103',
                'port'=>6330
            ],
            'slave_140'=>[
                'host'=>'192.168.20.103',
                'port'=>6340
            ]
        ]
    ];
    public $master_redis;
    public $slave_redis;

    public function __construct()
    {
        $config = $this->config;
        // 连接从服务redis
        $rand = mt_rand(0, (count($config['slaves'])-1));
        $this->slave_redis = new Redis();
        if ($rand){
            $this->slave_redis->connect($config['slaves']['slave_130']['host'],$config['slaves']['slave_130']['port']);//serverip port
        }else{
            $this->slave_redis->connect($config['slaves']['slave_140']['host'],$config['slaves']['slave_140']['port']);//serverip port
        }


        // 连接主服务器redis
        $this->master_redis = new Redis();
        $this->master_redis->connect($config['master']['host'],$config['master']['port']);//serverip port
    }

    // 读取
    public function get($key)
    {
        $res = $this->slave_redis->get($key);
        return $res;
    }

    // 写入
    public function set($key, $value)
    {
        $res = $this->master_redis->set($key, $value);
        return $res;
    }
}