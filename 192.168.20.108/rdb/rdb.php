<?php
require 'Input.php';
bgsave();

// rdb-save 保存方法
function save()
{
    rdbSave();
    call();
}


// rdb-bgsave 保存方法
function bgsave()
{
    $pid = pcntl_fork();

    pcntl_signal(SIGUSR1, function ($sig){
        Input::info('成功接收到子进程持久化信息, 并执行完成!');

    });

    if ($pid == 0){
        // 子进程
        rdbSave();
        posix_kill(posix_getpid(), SIGUSR1);
    }else{
        call();
    }
    pcntl_signal_dispatch();
}


// 实际保存rdb文件  保存方法
function rdbSave()
{
    Input::info('rdbSave 保存文件    开始');
    sleep(3);
    Input::info('rdbSave 保存文件    结束');
}


// 其他执行命令
function call()
{
    Input::info('rdbSave 持久化的时候 需要执行的命令');
}