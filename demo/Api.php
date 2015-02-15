<?php
/**
 * API请求类
 * 应用此类发起对服务器API接口的合法请求
 */
 
class Api {
	public $code = 200;//返回码
	public $msg = 'ok.';//返回信息
	public $data = '';//返回的数据
	public $debug = false;//开启DUBUG模式
	
	public function __construct($appid,$appkey){
		$this->appid = $appid;
		$this->appkey = $appkey;
	}
	
	/**
	 * 返回指定Api的数据
	 * @param String $url API地址
	 * @param Array $params 应用级参数 PS:这里只接收一维数组，不然会出错
	 * @param Bool $decode 是否解码
	 */
	public function getApiData($url,$params=array(),$decode = 1){
		$params['appid'] = $this->appid;
		$params['time'] = time();
		$params['ver'] = '1.0';
		$params['sign'] = $this->getSign($params);
		
		$json = $this->openSite($url,$this->getENCODEParams($params));
		if($decode){
			$data = json_decode($json,1);
			if(!$data){
				if($this->code != 404){
					$this->code = 500;
					$this->msg = '返回数据为空或者非JSON数据,请检查接口地址是否正确';
					$this->data = $json;
				}
				
				if(!$this->debug){
					die('Error:Data is empty!');
				}else{
					return false;
					
				}
			}
			
			if($data['code'] != 200){
				$this->code = $data['code'];
				$this->msg = $data['msg'];
				$this->data = $json;
			}
			
			return $data;
		}
		return $json;
	}
	
	
	/**
	 * 打开链接
	 * @param String $url 链接
	 * @param Array $params post参数
	 */
	public function openSite($url,$params=array()){
		$o="";  
		foreach ($params as $k=>$v){  
			$o.= "$k=".urlencode($v)."&"; 
		}
		
		$params=substr($o,0,-1);
		
		$ch = curl_init();  
		curl_setopt($ch, CURLOPT_POST, 1);  
		curl_setopt($ch, CURLOPT_HEADER, 0);  
		curl_setopt($ch, CURLOPT_URL,$url); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//此处设置 curl_exec 后直接返回数据，否则返回布尔值
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);//防止301重写向
		$result = curl_exec($ch);
		curl_close($ch);
		if(!$result){//打不开就中止
			$this->code = 404;
			$this->msg = '服务器接口连接失败，请检查接口地址是否正确！';
			if(!$this->debug) die('Error:Please check the server is normal!');
			
		}
		
		return $result;
	}
	
	/**
	 * 组装签名
	 * @param Array $params 参数数组
	 */
	protected function getSign($params = array()){
		ksort($params);
		$param = '';
		foreach($params as $key=>$val){
			$param .=  $key.$val;
		}
		return md5($param.$this->appkey);
	}
	
	/**
	 * 返回加密后的参数数组
	 * @param Array $params 参数数组
	 */
	protected function getENCODEParams($params = array()){
		$code = $join = '';
		foreach($params as $key => $val){
			$code .= $join.$key.'='.$val;
			$join = '&';
		}
		return array('code'=>$this->authCode($code,'ENCODE'),'appid'=>$this->appid);
	}
	
	/**
	 * 加解密函数
	 * @param String $string 要加(解)密的字符串
	 * @param String $operation ENCODE 加密 DECODE 解密 默认解密
	 * @param String $key 公钥
	 * @param String $expiry 
	 */
	protected function authCode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
		$ckey_length = 4;
		if($operation == 'DECODE') $string = str_replace(' ','+',$string);//防止编码出错
		
		$key = md5($key ? $key : $this->appkey);
		$keya = md5(substr($key, 0, 16));
		$keyb = md5(substr($key, 16, 16));
		$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

		$cryptkey = $keya.md5($keya.$keyc);
		$key_length = strlen($cryptkey);

		$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
		$string_length = strlen($string);

		$result = '';
		$box = range(0, 255);

		$rndkey = array();
		for($i = 0; $i <= 255; $i++) {
			$rndkey[$i] = ord($cryptkey[$i % $key_length]);
		}

		for($j = $i = 0; $i < 256; $i++) {
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}

		for($a = $j = $i = 0; $i < $string_length; $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}
		if($operation == 'DECODE') {
			if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
				return substr($result, 26);
			} else {
					return '';
				}
		} else {
			return $keyc.str_replace('=', '', base64_encode($result));
		}

	}

	/**
	 * 转义字符
	 */
	public function _stripslashes($string) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = $this->_stripslashes($val);
			}
		} else {
			$string = stripslashes($string);
		}
		return $string;
	}
	
	/**
	 * 自动补全API地址
	 * @param String $url 相对链接
	 */
	public function _getApiUrl($url){
		return 'http://192.168.1.201/test_api/index.php/'.$url;
	}
	
	
}