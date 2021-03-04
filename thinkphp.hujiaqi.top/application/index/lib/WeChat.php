<?php
namespace app\index\lib;
use think\Controller;
use \think\Session;

class WeChat extends Controller
{
	private $host = '';
	
	//定义成员属性
	public $str = '';
	public $postObj = '';
	public $FromUserName = '';
	public $ToUserName = '';
	public $contentStr = '';
	public $appid = '';
	public $appsecret = '';
	public $MsgType = '';
	public $Event = '';
	public $EventKey = '';
	public $json = '';
	public $post = '';
	
	//初始化操作
	public function __construct()
	{
		mfile(1, '操作类', 'wx', 'index');
		//获取消息的原始流
		$this->str = file_get_contents('php://input');
		//解析消息
	    libxml_disable_entity_loader(true);
	    $this->postObj = simplexml_load_string($this->str, 'SimpleXMLElement', LIBXML_NOCDATA);
	    mfile($this->postObj, '初始化', 'wx', 'index');
	    $this->json = json_encode($this->postObj);
        $this->post = json_decode($this->json,true);
        
	    //获取发送用户
	    $this->FromUserName = $this->postObj->FromUserName;
	    //获取接收用户
	    $this->ToUserName = $this->postObj->ToUserName;
	    //获取内容
	    $this->contentStr = $this->postObj->Content;
	    //获取消息类型
	    $this->MsgType = $this->postObj->MsgType;
	    //获取事件的key值
	    $this->EventKey = $this->postObj->EventKey;

	    switch ($this->MsgType) {
	    	case 'event':
	    		$this->Event = $this->postObj->Event;
	    		//$this->switch_event($this->Event);
	    		break;

	    	case 'text':
	    		$this->switch_text($this->contentStr,$this->FromUserName);
	    }
	}
	
	//接收消息类型为 text 时所执行的switch判断方法
	public function switch_text($content,$wxid)
	{
		switch ($content) {
			case '你是谁':
				//$res = $this->create_menu();
				$this->passiveMsg('我是张哲的老公');
				break;
				
			case '张哲是谁':
				$this->passiveMsg('是胡嘉麒心中的宝贝');
				break;
			
			default:
				$this->passiveMsg($content);
				break;
		}
	}
	
	public function passiveMsg($msg)
	{
		echo '<xml>
				  <ToUserName><![CDATA['.$this->FromUserName.']]></ToUserName>
				  <FromUserName><![CDATA['.$this->ToUserName.']]></FromUserName>
				  <CreateTime>'.time().'</CreateTime>
				  <MsgType><![CDATA[text]]></MsgType>
				  <Content><![CDATA['.$msg.']]></Content>
			  </xml>';
	}
}