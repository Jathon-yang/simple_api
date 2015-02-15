<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Member_model extends MY_Model{
    public function __construct(){
        parent::__construct();
		
    }
	
	/**
	 * 获取用户基本信息
	 * @param Int $uid 用户ID
	 */
	public function get_user_info($uid){
		return array('uid'=>1,'username'=>'admin');
	}
	
	/**
	 * 用户登录
	 * @param String $username 用户名
	 * @param String $password 密码
	 */
	public function login($username,$password){
		if($username == 'admin' && $password == 'admin'){
			return 1;
		}
		return 0;
	}

	
}