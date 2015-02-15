<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller {	
	public function error(){
		$this->_returnData(array(),'E9000');//用户验证失败
	}
	
}
