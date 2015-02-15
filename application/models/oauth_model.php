<?php
/**
 * token模型
 */
 
class Oauth_model extends MY_Model{
	public function __construct(){
		parent::__construct();
		
	}

	/**
	 * 获取用户信息
	 * $uid Int 用户ID
	 */
	 public function get_token($data){
		$time = time();
		$access_tokens = $this->create_access_token($data['uid'],$data['appkey']);
		
		$token['uid'] = strval($data['uid']);
		
		$token['access_token'] = $access_tokens['access_token'];
		$token['refresh_token'] = md5($token['access_token'].mt_rand(11,99).$time);
		
		$token['atime'] = $access_tokens['time'];//存活两小时
		$token['rtime'] = $time + 2592000;//存活三十天
		
		
		$modeldata['appid'] = $data['appid'];
		$modeldata['access_token'] = $token['access_token'];
		$modeldata['refresh_token'] = $token['refresh_token'];
		$modeldata['a_time'] = $token['atime'];
		$modeldata['r_time'] = $token['rtime'];
		
		$success = 0;
		$query = $this->db->get_where('oauth', array('uid' => $data['uid']), 1, 0);
		$db_data = $query->row_array();
		
		if(empty($db_data['uid'])){
			$modeldata['uid'] = $data['uid'];
			if($this->db->insert('oauth',$modeldata)){
				$success = 1;
			}
		}else{
			$this->db->where('uid',$db_data['uid']);
			if($this->db->update('oauth',$modeldata)){
				$success = 1;
			}
		}
		if($success == 1) return $token;
		
		return array();
		
	}
	
	/**
	 * 获取授权详情
	 * @param Int $uid 用户ID
	 * @param Int $appid 应用ID
	 * @param Int $access_token 授权码
	 */
	public function get_info($uid,$appid,$access_token){
		$query = $this->db->get_where('oauth', array('uid' => $uid,'appid'=>$appid,'access_token'=>$access_token), 1, 0);
		
		return $query->row_array();
	}
	
	/**
	 * 刷新access_token
	 * @param String $uid 用户ID
	 * @param String $appkey 应用密钥
	 * @param String $refresh_token 刷新值
	 */
	public function refresh_token($uid,$appkey,$refresh_token){
		$code = 200;
		$msg = '';
		
		$query = $this->db->get_where('oauth', array('uid'=>$uid,'refresh_token' => $refresh_token), 1, 0);
		$data = $query->row_array();
		
		if(empty($data['uid'])){
			$code = 'E2007';
			$msg = 'refresh_token非法';
		}else{
			if($data['r_time'] < time()){
				$code = 'E2008';
				$msg = 'refresh_token已过期';
			}
		}
		
		$access_tokens = array();
		if($code == 200){
			$access_tokens = $this->create_access_token($uid,$appkey);
			
			$modeldata['access_token'] = $access_tokens['access_token'];
			$modeldata['a_time'] = $access_tokens['time'];
			
			$this->db->where('uid',$data['uid']);
			$this->db->update('oauth',$modeldata);
		}
		
		$result['code'] = $code;
		$result['msg'] = $code != 200 ? $msg : $access_tokens;
		return $result;
	}
	
	/**
	 * 生成access_token
	 * @param Int $uid 用户ID
	 * @param Int $appkey 应用密钥
	 */
	public function create_access_token($uid,$appkey){
		$time = time();
		return array('time'=>$time + 7200,'access_token'=>md5($uid.mt_rand(11,99).$appkey.$time));
	}
	 
}