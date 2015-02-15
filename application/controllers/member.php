<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Member extends MY_Controller {
	public function __construct(){
	
		parent::__construct();
		
		$this->load->model('Member_model', 'M');
		
		$routes = array();//不用授权就能访问的页面
		
		$uid = intval($this->input->post('uid'));
		if(!in_array($this->router->fetch_method(),$routes)){
			
			$access_token = trim($this->input->post('access_token'));
			$this->_isEmpty($uid,'E2003');
			$this->_isEmpty($access_token,'E2004');
			
			$this->load->model('Oauth_model', 'oauth');
			$token_info = $this->oauth->get_info($uid,$this->appid,$access_token);
			
			if(empty($token_info['uid'])){
				$this->_returnData(array(),'E2005');//授权验证失败
			}
			
			if($token_info['a_time'] < time()){
				$this->_returnData(array(),'E2006');//access_token已过期
			}
			
		}
		
		if($uid){
			$this->user = $this->M->get_user_info($uid);
		}
		
	}
	
	//获取用户信息
	public function userinfo(){
		
		$this->_returnData($this->user,'200');//access_token已过期
	}
	
}
