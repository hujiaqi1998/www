<?php
require 'Input.php';

class RedisMS{
    public $master_redis;
    public $slave_redis;
    public $connect_info;
    public $slaveIndex;

    public function __construct($config)
    {
        // 连接主服务器redis
        $this->createMasterConnect($config);
        // 创建从服务redis
        $this->createSlaveConnect($config);
        // 每过2秒检测一次主从延迟
        $this->maintain();

        Input::info($this->connect_info, "这是获取的连接");
    }

    # 初始连接集合方法
    public function maintain()
    {
        $masterRedis = $this->connect_info['master'];
        swoole_timer_tick(2000, function ($timer_id) use($masterRedis){
            // 得到主节点的连接信息
            $replInfo = $masterRedis->info('replication');
            //Input::info($replInfo, "节点信息");
            // 得到主节点偏移量
            $masterOffset = $replInfo['master_repl_offset'];
            // 记录新增的从节点
            $slaves['slaves'] = [];
            for ($i=0; $i < $replInfo['connected_slaves']; $i++) {
                // 获取slave的信息
                $slaveInfo = $this->stringToArr($replInfo['slave'.$i]);
                $slaveFlag = $slaveInfo['ip'].':'.$slaveInfo['port'];
                // 延迟检测
                if (($masterOffset - $slaveInfo['offset']) < 50) {
                    // 是正常范围
                    // 如果之前因为网络延迟删除了节点，现在恢复了网络 -》新增
                    // 这是动态新增
                    if (!isset($this->connect_info['slaves'][$slaveFlag])) {
                        $slaves['slaves']['slave'.$i] = [
                            'host' => $slaveInfo['ip'],
                            'port' => $slaveInfo['port']
                        ];
                        Input::info($slaveFlag, "新的从节点");
                    }
                } else {
                    // 延迟 -> 删除节点
                    Input::info($slaveFlag, "删除节点");
                    unset($this->connect_info['slaves'][$slaveFlag]);
                }
            }
            // 创建从服务redis
            $this->createSlaveConnect($slaves);
        });
    }

    # 连接redis方法
    public function getRedis($ip, $port)
    {
        $redis = new Redis();
        $redis->connect($ip, $port);
        return $redis;
    }

    # 创建主节点连接方法
    private function createMasterConnect($config)
    {
        // 连接主服务器redis
        $redis = $this->getRedis($config['master']['host'], $config['master']['port']);
        $this->connect_info['master'] = $redis;
    }

    # 创建从节点连接方法
    private function createSlaveConnect($config)
    {
        // 连接从服务器redis

        foreach ($config['slaves'] as $k=>$v){
            $redis = $this->getRedis($v['host'], $v['port']);
            $slave = $v['host'].':'.$v['port'];
            $this->connect_info['slaves'][$slave] = $redis;
            $this->slaveIndex[$slave] = [
                'host'=>$v['host'],
                'port'=>$v['port']
            ];
        }
    }

    protected function stringToArr($str, $flag1 = ',', $flag2 = '=')
    {
        // "ip=192.160.1.130,port=6379,state=online,offset=72574,lag=0"
        $arr = explode($flag1, $str);
        $ret = [];
        // $key ip
        // $value 192.160.1.130
        foreach ($arr as $key => $value) {
            $arr2 = explode($flag2, $value);
            $ret[$arr2[0]] = $arr2[1];
        }
        return $ret;
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