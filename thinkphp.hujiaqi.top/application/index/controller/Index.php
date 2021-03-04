<?php
namespace app\index\controller;
use \think\Request;
use DawnApi\facade\ApiController;
use think\Controller;
use app\index\lib\WxConnect;
use app\index\lib\WeChat;
class Index extends Controller
{
    public function index()
    {
        define("TOKEN", "thinkphpToken");
        mfile(1, '入口', 'wx', 'index');
		if (!empty($_REQUEST['echostr'])) {
	        $WX_COMMON = new WxConnect();
	        $WX_COMMON->valid();
	    }

        $request = $_REQUEST;
        mfile($request, '入口微信参数', 'wx', 'index');
        $res = $this->checkSignature($request);
        mfile($res, '入口参数', 'wx', 'index');
        if ($res) {
        	mfile(1,'实例化操作类之前', 'wx', 'index');
            $wx = new WeChat();
            mfile($wx, '入口初始化类', 'wx', 'index');
        }else{
            
        }
    }
    
    public function test()
    {
    	mfile('xixi','test','wx','test');
    }
    
    private function checkSignature($data)
    {
        $token = 'thinkphpToken';
        $signature = $data['signature'];
        $timestamp = $data['timestamp'];
        $nonce = $data['nonce'];

        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);

        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if($tmpStr == $signature){
            return true;
        }else{
            return false;
        }
    }
    
    private function mfile($mfile_value,$mfile_name = '',$path = '',$filename = '',$from = false){
        $path_out = dirname(dirname(__DIR__)).'/runtime/logs';
        if(!empty($path)){
            $path_out .= preg_match('^//^',$path) === 1 ? $path : '/'.$path;
        }

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
        	$path_out=$this->change($path_out);
        }
        
        if($from) {
        	$from = "from:".$_SERVER['QUERY_STRING']."\r\n";
        }
        if($mfile_name) {
        	$mfile_name .= ':';
        }
        $res = file_put_contents($path_out,"[".date("Y-m-d H:i:s")."] ".$from.$mfile_name.print_r($mfile_value,true)."\r\n\r\n",FILE_APPEND);
    }
    
    private function change($file_names)
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
}
