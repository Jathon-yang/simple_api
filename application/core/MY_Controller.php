<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 自定义Controller
 */
class MY_Controller extends CI_Controller{//前台
	
    public function __construct(){
		
        parent::__construct();
		$this->config->load('oauth_code');
		$this->oauth_codes = $this->config->config['oauth']; 
		
		$appid = intval($this->input->post('appid'));
		$this->_isEmpty($appid,'E1000');
		
		
		
		$this->load->model('App_model', 'app');//加载模型
		$app = $this->app->get_info($appid);
		
		if(empty($app)){
			$this->_returnData(array(),'E1004');//应用ID错误
		}
		$this->appid = $app['appid'];
		$this->appkey = $app['appkey'];
		
		if($code = $this->input->post('code')){//开启参数加密
			$_POST = array();
			parse_str(_stripslashes(_authcode($code,'DECODE',$this->appkey)),$_POST);//解密字符
		}
		
		$sign = $this->input->post('sign');
		$time = $this->input->post('time');
		$ver = $this->input->post('ver');
		
		$this->_isEmpty($sign,'E1001');
		$this->_isEmpty($time,'E1002');
		$this->_isEmpty($ver,'E1006');
		
		if(abs(time() - $time) > 300){
			$this->_returnData(array(),'E1003');//请求超时
		}
		
		if($sign != $this->_getSign()){
			$this->_returnData(array(),'E1005');//非法签名
		}
		
		
	}
	
	/**
	 * 返回格式化数据
	 */
	public function _returnData($data,$code='',$msg=''){
		
		$data['code'] = 200;
		if(!empty($msg)){//自定义提示归入其他错误
			$data['code'] = 'E9000';
			$data['msg'] = $msg;
		}elseif($code != 200){
			$data['code'] = $code;
			$data['msg'] = $this->oauth_codes[$code];
		}
		
		$output = $this->output->set_content_type('application/json')->set_output(json_encode($data))->get_output();
		echo $output;
		exit;
	}
	
	/**
	 * 判断参数是否为空，空则返回错误并中止
	 */
	public function _isEmpty($val,$code,$msg=''){
		if(empty($val)){
			$this->_returnData('',$code,$msg);
		}
	}
	
	/**
	 * 组装签名
	 */
	public function _getSign(){
		$params = $this->input->post();
		ksort($params);
		unset($params['sign']);//排除签名
		$param = '';
		foreach($params as $key=>$val){
			$param .=  $key.$val;
		}
		return md5($param.$this->appkey);
	}
}
