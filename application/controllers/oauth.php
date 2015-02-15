<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Oauth extends MY_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->model('Oauth_model', 'oauth');
	}
	
	//首页
	public function index(){
		
	}
	
	//获取授权token
	public function token(){
		$username = trim($this->input->post('username'));
		$password = trim($this->input->post('password'));
		$this->_isEmpty($username,'E2000');
		$this->_isEmpty($password,'E2001');
		
		$this->load->model('Member_model', 'M');
		if($uid = $this->M->login($username,$password)){
			
			$data['uid'] = $uid;
			$data['appkey'] = $this->appkey;
			$data['appid'] = $this->appid;
			
			$token = $this->oauth->get_token($data);
			$this->_returnData($token,'200');//成功
		}
		$this->_returnData(array(),'E2002');//用户验证失败
	}
	
	//刷新token
	public function refresh(){
		$uid = trim($this->input->post('uid'));
		$refresh_token = trim($this->input->post('refresh_token'));
		$this->_isEmpty($uid,'E2003');
		$this->_isEmpty($refresh_token,'E2007');
		
		$data = $this->oauth->refresh_token($uid,$this->appkey,$refresh_token);
		
		
		$msg = $data['code'] == 200 ? $data['msg'] : array();
		$this->_returnData($msg,$data['code']);//用户验证失败
	}
	
	//测试客户端能不能正常连接服务端接口
	public function ping(){
		$this->_returnData(array(),200);//能到达这里表示成功
	}
}
