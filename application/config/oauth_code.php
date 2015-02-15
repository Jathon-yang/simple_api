<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 错误码集合
 */
/* 系统级错误 */
$config['oauth']['E1000'] = '缺少应用ID';
$config['oauth']['E1001'] = '缺少签名';
$config['oauth']['E1002'] = '缺少请求时间';
$config['oauth']['E1003'] = '请求超时';
$config['oauth']['E1004'] = '应用ID错误';
$config['oauth']['E1005'] = '非法签名';
$config['oauth']['E1006'] = '缺少版本号';

/* 应用级错误 */
$config['oauth']['E2000'] = '用户名非法';
$config['oauth']['E2001'] = '密码非法';
$config['oauth']['E2002'] = '用户验证失败';
$config['oauth']['E2003'] = '缺少用户ID';
$config['oauth']['E2004'] = '缺少授权符';
$config['oauth']['E2005'] = '授权验证失败';
$config['oauth']['E2006'] = 'access_token已过期';
$config['oauth']['E2007'] = 'refresh_token非法';
$config['oauth']['E2008'] = 'refresh_token已过期';

/*内部数据库错误*/
$config['oauth']['E4000'] = '内部错误';

/* 其他错误 */
$config['oauth']['E9000'] = '未知错误';
$config['oauth']['E9001'] = '权限不足';