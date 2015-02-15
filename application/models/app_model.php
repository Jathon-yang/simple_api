<?php
/**
 * 用户模型
 */
 
class App_model extends MY_Model{
	public function __construct(){
		parent::__construct();
		
	}

	/**
	 * 获取用户信息
	 * $uid Int 用户ID
	 */
	 public function get_info($appid){
		$this->db->from('app')
		->where(array('appid'=>$appid))
		->limit(1);
		
		$query = $this->db->get();
		return $query->row_array();
	 } 
	 
}