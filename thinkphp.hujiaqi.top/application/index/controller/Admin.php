<?php
namespace app\index\controller;
use \think\Request;
use DawnApi\facade\ApiController;
use think\Controller;
use \think\Session;
use app\index\lib\Common;
use \think\Db;

class Admin extends Common
{
	public function index()
	{
		return $this->fetch();
	}
	
	public function test()
	{
		echo 'sftp';
		$session = Session::get();
		var_dump($session);
	}
	
	public function user_list(Request $request)
	{
		$get = $_GET;
		
		$where = array();
		
		$where['delete'] = array('eq', 0);
		$where['id'] = array('neq', 1);
		
		$qv = ((isset($get['start']) && !empty($get['start'])) && (isset($get['end']) && !empty($get['end'])));
		
		if ($qv) {
			$where['date'] = [array('egt',$get['start'].' 00:00:00'),array('elt',$get['end'].' 23:59:59')];
		}else{
			if (isset($get['start']) && !empty($get['start'])){
				$where['date'] = array('egt', $get['start'].' 00:00:00');
			}
			
			if (isset($get['end']) && !empty($get['end'])){
				$where['date'] = array('elt', $get['end'].' 23:59:59');
			}	
		}
		
		if (isset($get['phone']) && !empty($get['phone'])){
			$where['phone'] = array('eq', $get['phone']);
		}
		
		if (isset($get['user_id']) && !empty($get['user_id'])){
			$where['id'] = array('eq', $get['user_id']);
		}
		
		//获取当前页
		$page = $request->param('page');
		
		//定义每页显示条数
		$total = 5;
		//获取总条数
		$count = Db::table('user_list')->where($where)->count();
		//计算总页数
		$pageAll = intval(ceil($count/$total));
		
		if ($page > $pageAll){
			$page = 1;
		}
		
		//计算上一页
		if ($pageAll == 1){
			$pagePre = 1;
			$pageNext = 1;
		}else{
			if ($page == 1){
				$pagePre = 1;
				$pageNext = $page + 1;
			}else{
				$pagePre = $page - 1;
				if ($page == $pageAll){
					$pageNext = $pageAll;
				}else{
					$pageNext = $page + 1;
				}
			}
		}
		
		switch ($pageAll) {
			case 1:
				$pageList = [1];
				break;
				
			case 2:
				$pageList = [1,2];
				break;
				
			case 3:
				$pageList = [1,2,3];
				break;
			
			default:
				if ($page == 1){
					$pageList = [1,2,3];
				}else if ($page == $pageAll){
					$pageList = [($pageAll - 2), ($pageAll - 1), $pageAll];
				}else{
					$pageList = [($page - 1), $page, ($page + 1)];
				}
				break;
		}
		
		//判断是否传入参数，用于分页时使用
		$str = $this->splicing_http_param($get, ['start','end','phone','user_id']);
		
		if ($str){
			$this->assign('http_param', $str);
		}else{
			$this->assign('http_param', '');
		}
		
		$data = Db::table('user_list')->where($where)->limit(($page - 1)*$total,$total)->select();
		
		$this->assign('data', $data);
		$this->assign('page', $page);
		$this->assign('pageAll', $pageAll);
		$this->assign('pagePre', $pagePre);
		$this->assign('pageNext', $pageNext);
		$this->assign('pageList', $pageList);
		
		
		return $this->fetch();
	}
	
	public function deduction(Request $request)
	{
		$id = $request->param('id');
		
		$info = Db::table('user_list')->where('id',$id)->find();
		
		$this->assign('balance', $info['balance']);
		$this->assign('user_info', $info);
		$this->assign('id',$id);
		return $this->fetch();
	}
	
	public function go_deduction(Request $request)
	{
		$id = $request->param('id');
		
		$money = $request->param('money');
		
		if (empty($id)){
			echo json_encode([
				'state'=>2,
				'error_code' => 400,
				'error_msg'=>'参数错误！'
			],JSON_UNESCAPED_UNICODE);
			exit();
		}
		
		if ($money < 0 || empty($money)){
			echo json_encode([
				'state'=>2,
				'error_code' => 400,
				'error_msg'=>'扣款金额不能小于0元！'
			],JSON_UNESCAPED_UNICODE);
			exit();
		}
		
		$user_info = Db::table('user_list')->where('id', $id)->find();
		
		if ($user_info['balance'] < $money){
			echo json_encode([
				'state'=>2,
				'error_code' => 400,
				'error_msg'=>'会员账户余额不足！'
			],JSON_UNESCAPED_UNICODE);
			exit();
		}
		
		$res = Db::table('user_list')->where('id', $id)->setDec('balance', $money);
		
		$order_num = date('YmdHis').mt_rand(1000,9999).$id;
		$order_data = [
				'order_num'=>$order_num,
				'user_id'=>$id,
				'money'=>$money,
				'date'=>date('Y-m-d H:i:s'),
				'order_type'=>2,
				'remark'=>'会员ID为'.$id.'的会员扣款'.$money.'元'
			];
		$add_order_res = Db::table('order_list')->insert($order_data);
		
		if ($res){
			echo json_encode([
				'state'=>1,
				'error_code' => 200,
				'error_msg'=>'结账成功！'
			],JSON_UNESCAPED_UNICODE);
			exit();
		}else{
			echo json_encode([
				'state'=>2,
				'error_code' => 400,
				'error_msg'=>'结账失败！'
			],JSON_UNESCAPED_UNICODE);
			exit();
		}
	}
	
	public function add_order()
	{
		return $this->fetch();
	}
	
	public function go_add_order(Request $request)
	{
		$money = $request->param('money');
		
		if ($money <= 0 || empty($money)){
			echo json_encode([
				'state'=>2,
				'error_code' => 400,
				'error_msg'=>'充值金额需大于0元！'
			],JSON_UNESCAPED_UNICODE);
			exit();
		}
		
		$order_num = date('YmdHis').mt_rand(1000,9999).'1';
		
		$data = [
				'order_num' => $order_num,
				'user_id'=>1,
				'money'=>$money,
				'date'=>date('Y-m-d H:i:s'),
				'order_type'=>3,
				'remark'=>'非会员顾客消费'.$money.'元'
			];
		
		$res = Db::table('order_list')->insert($data);
		
		if ($res){
			echo json_encode([
				'state'=>1,
				'error_code' => 200,
				'error_msg'=>'添加成功！'
			],JSON_UNESCAPED_UNICODE);
			exit();
		}else{
			echo json_encode([
				'state'=>2,
				'error_code' => 400,
				'error_msg'=>'添加失败！'
			],JSON_UNESCAPED_UNICODE);
			exit();
		}
	}
	
	public function recharge(Request $request)
	{
		$id = $request->param('id');
		
		$info = Db::table('user_list')->where('id',$id)->find();
		
		$this->assign('balance', $info['balance']);
		$this->assign('id',$id);
		
		return $this->fetch();
	}
	
	public function go_recharge(Request $request)
	{
		$id = $request->param('id');
		
		$money = $request->param('money');
		
		if (empty($id)){
			echo json_encode([
				'state'=>2,
				'error_code' => 400,
				'error_msg'=>'参数错误！'
			],JSON_UNESCAPED_UNICODE);
			exit();
		}
		
		if ($money <= 0 || empty($money)){
			echo json_encode([
				'state'=>2,
				'error_code' => 400,
				'error_msg'=>'充值金额需大于0元！'
			],JSON_UNESCAPED_UNICODE);
			exit();
		}
		
		$res = Db::table('user_list')->where('id', $id)->setInc('balance', $money);
		
		$order_num = date('YmdHis').mt_rand(1000,9999).$id;
		$order_data = [
				'order_num'=>$order_num,
				'user_id'=>$id,
				'money'=>$money,
				'date'=>date('Y-m-d H:i:s'),
				'order_type'=>1,
				'remark'=>'会员ID为'.$id.'的会员充值'.$money.'元'
			];
		$add_order_res = Db::table('order_list')->insert($order_data);
		
		if ($res){
			echo json_encode([
				'state'=>1,
				'error_code' => 200,
				'error_msg'=>'充值成功！'
			],JSON_UNESCAPED_UNICODE);
			exit();
		}else{
			echo json_encode([
				'state'=>2,
				'error_code' => 400,
				'error_msg'=>'充值失败！'
			],JSON_UNESCAPED_UNICODE);
			exit();
		}
	}
	
	public function edit(Request $request)
	{
		$id = $request->param('id');
		
		$user_info = Db::table('user_list')->where('id', $id)->find();
		
		$this->assign('id', $id);
		$this->assign('user_info', $user_info);
		
		return $this->fetch();
	}
	
	public function edit_user(Request $request)
	{
		$param = $request->param();
		
		if (empty($param['id'])){
			echo json_encode([
				'state'=>2,
				'error_code' => 400,
				'error_msg'=>'参数错误！'
			],JSON_UNESCAPED_UNICODE);
			exit();
		}
		
		if (empty($param['username'])){
			echo json_encode([
				'state'=>2,
				'error_code' => 400,
				'error_msg'=>'请填写姓名！'
			],JSON_UNESCAPED_UNICODE);
			exit();
		}
		
		if (empty($param['phone']) || strlen($param['phone']) != 11){
			echo json_encode([
				'state'=>2,
				'error_code' => 400,
				'error_msg'=>'请输入正确的手机号码！'
			],JSON_UNESCAPED_UNICODE);
			exit();
		}
		
		if (!in_array($param['sex'], [0,1,2])){
			echo json_encode([
				'state'=>2,
				'error_code' => 400,
				'error_msg'=>'请选择性别！'
			],JSON_UNESCAPED_UNICODE);
			exit();
		}
		
		$update = [
				'user_name'=>$param['username'],
				'phone'=>$param['phone'],
				'user_sex'=>$param['sex']
			];
		
		$res = Db::table('user_list')->where('id', $param['id'])->update($update);
		
		if ($res){
			echo json_encode([
				'state'=>1,
				'error_code' => 200,
				'error_msg'=>'修改成功'
			],JSON_UNESCAPED_UNICODE);
			exit();
		}else{
			echo json_encode([
				'state'=>2,
				'error_code' => 400,
				'error_msg'=>'修改失败！'
			],JSON_UNESCAPED_UNICODE);
			exit();
		}
	}
	
	public function order_list(Request $request)
	{
		$get = $_GET;
		
		$where = array();
		
		$qv = ((isset($get['start']) && !empty($get['start'])) && (isset($get['end']) && !empty($get['end'])));
		
		$nqv = ((!isset($get['start']) || empty($get['start'])) && (!isset($get['end']) || empty($get['end'])));
		
		if ($qv) {
			//$where['date'] = [array('egt', $get['start'].' 00:00:00'),array('elt', $get['end'].' 23:59:59')];
			$condition = [$get['start'].' 00:00:00', $get['end'].' 23:59:59'];
			$formula = 'between';
		
		}else if($nqv){
			$condition = ['1900-01-01 00:00:00', date('Y-m-d H:i:s', strtotime("+20 day"))];
			$formula = 'between';
		}else{
			if (isset($get['start']) && !empty($get['start'])){
				//$where['o.date'] = array('egt', $get['start'].' 00:00:00');
				$condition = $get['start'].' 00:00:00';
				$formula = '>=';
			}
			
			if (isset($get['end']) && !empty($get['end'])){
				//$where['o.date'] = array('elt', $get['end'].' 23:59:59');
				$condition = $get['end'].' 23:59:59';
				$formula = '<=';
			}
		}
		
		if (isset($get['order_type']) && !empty($get['order_type'])){
			$where['order_type'] = array('eq', $get['order_type']);
		}
		
		if (isset($get['order_num']) && !empty($get['order_num'])){
			$where['order_num'] = array('eq', $get['order_num']);
		}
		
		//获取当前页
		$page = $request->param('page');
		//定义每页显示条数
		$total = 5;
		//获取总条数
		$count = Db::table('order_list')->where($where)->whereTime('date', $formula, $condition)->count();
		
		//计算总页数
		$pageAll = ceil($count/$total);
		
		if ($page > $pageAll){
			$page = 1;
		}
		
		//计算上一页
		if ($pageAll == 1){
			$pagePre = 1;
			$pageNext = 1;
		}else{
			if ($page == 1){
				$pagePre = 1;
				$pageNext = $page + 1;
			}else{
				$pagePre = $page - 1;
				if ($page == $pageAll){
					$pageNext = $pageAll;
				}else{
					$pageNext = $page + 1;
				}
			}
		}
		
		switch ($pageAll) {
			case 1:
				$pageList = [1];
				break;
				
			case 2:
				$pageList = [1,2];
				break;
				
			case 3:
				$pageList = [1,2,3];
				break;
			
			default:
				if ($page == 1){
					$pageList = [1,2,3];
				}else if ($page == $pageAll){
					$pageList = [($pageAll - 2), ($pageAll - 1), $pageAll];
				}else{
					$pageList = [($page - 1), $page, ($page + 1)];
				}
				break;
		}
		
		//判断是否传入参数，用于分页时使用
		$str = $this->splicing_http_param($get, ['start','end','order_type','order_num']);
		
		if ($str){
			$this->assign('http_param', $str);
		}else{
			$this->assign('http_param', '');
		}
		

		$data = Db::table('order_list')
				->alias('o')
				->join('user_list u', 'u.id=o.user_id')
				->where($where)
				->whereTime('o.date', $formula, $condition)
				->field('o.*, u.user_name')
				->limit(($page - 1)*$total,$total)
				->select();
		
		$this->assign('order_list', $data);
		$this->assign('page', $page);
		$this->assign('pageAll', $pageAll);
		$this->assign('pagePre', $pagePre);
		$this->assign('pageNext', $pageNext);
		$this->assign('pageList', $pageList);
		
		return $this->fetch();
	}
	
	public function out_login()
	{
		Session::clear();
		
		echo json_encode([
				'state'=>1,
				'error_code' => 200,
				'error_msg'=>'success'
			]);
	}
	
	public function login(Request $request)
	{
		$get = $request->param('state');
		
		$this->assign('state', $get);
		return $this->fetch();
	}
	
	public function go_login()
	{
		$data = $_POST;
		
		if (empty($data['username'])){
			echo json_encode([
					'state'=>2,
					'error_code'=>400,
					'error_msg'=>'用户名是空!'
				]);
			return false;
		}
		
		if (empty($data['password'])){
			echo json_encode([
					'state'=>2,
					'error_code'=>400,
					'error_msg'=>'密码是空!'
				]);
			return false;
		}
		
		if ($data['username'] != 'admin'){
			echo json_encode([
					'state'=>2,
					'error_code'=>401,
					'error_msg'=>'用户名不存在!'
				]);
			return false;
		}
		
		if ($data['password'] != 'abcdefg'){
			echo json_encode([
					'state'=>2,
					'error_code'=>402,
					'error_msg'=>'密码错误!'
				]);
			return false;
		}
		
		Session::set('user',$data['username']);
		
		$json = json_encode([
				'state'=>1,
				'error_msg'=>'success',
				'error_code'=>200
			]);
		
		echo $json;
	}
	
	public function delete_user()
	{
		$data = $_POST;
		
		if (!isset($data['id']) || empty($data['id'])){
			echo json_encode([
					'state'=>2,
					'error_code'=>400,
					'error_msg'=>'参数错误！'
				],JSON_UNESCAPED_UNICODE);
			exit();
		}
		
		$where = array();
		$where['delete'] = array('eq', 0);
		$where['id'] = array('eq', $data['id']);
		
		$count = Db::table('user_list')->where($where)->count();
		
		if ($count < 1){
			echo json_encode([
					'state'=>2,
					'error_code'=>400,
					'error_msg'=>'会员不存在！'
				],JSON_UNESCAPED_UNICODE);
			exit();
		}
		
		$amdin = Session::get('user');
		
		
		
		$del_res = Db::table('user_list')->where($where)->update(['delete'=>1]);
		
		if ($del_res){
			echo json_encode([
					'state'=>1,
					'error_code'=>200,
					'error_msg'=>'删除成功！'
				],JSON_UNESCAPED_UNICODE);
			exit();
		}else{
			echo json_encode([
					'state'=>2,
					'error_code'=>400,
					'error_msg'=>'删除失败！'
				],JSON_UNESCAPED_UNICODE);
			exit();
		}
	}
	
	private function splicing_http_param(array $param=array(), array $field=array())
	{
		if (empty($param)){
			return false;
		}
		
		$str = '';
		
		if (empty($field)){
			foreach ($param as $key=>$value){
				$str .= '&'.$key.'='.$value;
			}
		}else{
			foreach ($param as $key=>$value){
				if (in_array($key,$field)){
					$str .= '&'.$key.'='.$value;
				}
			}
		}
		
		if ($str == ''){
			return false;
		}else{
			return $str;
		}
	}
}