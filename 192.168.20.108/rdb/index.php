<?php
$pid = pcntl_fork();

if ($pid == 0){
    var_dump('子进程');
}else{
    var_dump('父进程');
}

while(true){}