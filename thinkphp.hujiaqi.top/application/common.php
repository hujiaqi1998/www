<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +---------------------------------------------------------thinkphp.hujiaqi.top-------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function mfile($mfile_value,$mfile_name = '',$path = '',$filename = '',$from = false){
    $path_out = dirname(dirname(__DIR__)).'/thinkphp.hujiaqi.top/runtime/logs';
    if(!empty($path)){
        $path_out .= preg_match('^//^',$path) === 1 ? $path : '/'.$path;
    }
	var_dump($path_out);
	
    if(!is_dir($path_out)){
    	$res = mkdir($path_out, 0777, true);
    }
    if(!empty($filename)){
        $path_out .= preg_match('/\/$/',$path_out) === 1 ? $filename : '/'.$filename;
    }else{
        $path_out .= '/mjs';
    }

    // $path_out .= '_'.date("YmdH").'.log';
    $path_out .= '_'.date("Ymd").'_1'.'.log';

    if (is_dir($path_out)){
    	$path_out=change($path_out);
    }
    
    if($from) {
    	$from = "from:".$_SERVER['QUERY_STRING']."\r\n";
    }
    if($mfile_name) {
    	$mfile_name .= ':';
    }
    $res = file_put_contents($path_out,"[".date("Y-m-d H:i:s")."] ".$from.$mfile_name.print_r($mfile_value,true)."\r\n\r\n",FILE_APPEND);
}

function change($file_names)
{
    $filesize=filesize(trim($file_names));
    if($filesize >= 1048576){
        $filesize = round($filesize / 1048576 * 100) / 100 ;
        $file_name=substr($file_names,0,strrpos($file_names,"."));
        $name= substr($file_name,0,strrpos($file_name,"_"));
        $num = substr($file_name,strripos($file_name,"_")+1);
        if($filesize>3){
            $num+=1;
            $file_names=$name.'_'.$num .".log";
            return change($file_names);
        }
    }
    return $file_names;
}