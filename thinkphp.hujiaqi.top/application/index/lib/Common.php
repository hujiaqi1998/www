<?php
namespace app\index\lib;
use think\Controller;
use \think\Request;
use DawnApi\facade\ApiController;
use \think\Session;

class Common extends Controller
{
	public function _initialize()
	{
		parent::_initialize();
		
		$user = Session::get('user');
		
		$action = request()->action();
		
		$arr = ['login','go_login','out_login'];
		
		if ($user != 'admin'){
			if (!in_array($action, $arr)){
				$this->error('登录过期，请重新登录！','http://thinkphp.hujiaqi.top/index/admin/login/state/2');	
			}
		}
	}
	
	public function mfile($mfile_value,$mfile_name = '',$path = '',$filename = '',$from = false){
        $path_out = dirname(dirname(__DIR__)).'/Runtime/Logs';
        if(!empty($path)){
            $path_out .= preg_match('^/',$path) === 1 ? $path : '/'.$path;
        }
        if(!is_dir($path_out)) mkdir($path_out);
        if(!empty($filename)){
            $path_out .= preg_match('/$',$path_out) === 1 ? $filename : '/'.$filename;
        }else{
            $path_out .= '/mjs';
        }
        // $path_out .= '_'.date("YmdH").'.log';
        $path_out .= '_'.date("Ymd").'_1'.'.log';
        $path_out=change($path_out);

        if($from) $from = "from:".$_SERVER['QUERY_STRING']."\r\n";
        if($mfile_name) $mfile_name .= ':';
        file_put_contents($path_out,"[".date("Y-m-d H:i:s")."] ".$from.$mfile_name.print_r($mfile_value,true)."\r\n\r\n",FILE_APPEND);
    }
}