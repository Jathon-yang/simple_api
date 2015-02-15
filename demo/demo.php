<?php 
require_once('./Api.php');

/*配置请求基本参数 这里的信息需要和 api_app 表中的数据一致才行*/
$appid = 1;//应用ID
$appkey = 123456;//应用密钥

/*初始化请求类*/
$api = new Api($appid,$appkey);

/*获取授权*/
$url = $api->_getApiUrl('oauth/token');//组装请求链接
$data = array();
$data['username'] = 'admin';
$data['password'] = 'admin';//组装请求参数
$tokens = $api->getApiData($url,$data);//获取请求结果

/* 在实际环境中最好要做 atime 和 rtime 的超时判断 */
if(isset($tokens['code']) && $tokens['code'] == 200){
	/*根据授权获取用户信息*/
	$url = $api->_getApiUrl('member/userinfo');//组装请求链接
	$user_data = array();
	$user_data['uid'] = $tokens['uid'];
	$user_data['access_token'] = $tokens['access_token'];//组装请求参数
	$users = $api->getApiData($url,$user_data);//获取用户信息数组

	print_r($users);exit;
}else{
	print_r($tokens);exit;
}