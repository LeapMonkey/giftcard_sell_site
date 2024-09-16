<?php
/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_login(){
	$user = session('user_auth');
	$user_info = cookie('user_info');

	if (empty($user)){
	 	if($user_info){

                session('user_auth',$user_info);
                session('userId',$user_info['id']);
                $user = session('user_auth');
            }
	 }
	   
	if (empty($user)) {
		return 0;
	} else {
		return $user;
	}
}


/**
 * 获取用户登录名
 * @param  integer $uid 用户ID
 * @return string       用户名
 */
function get_loginname(){
	if(is_login()){
		return session('user_auth.login_name')?session('user_auth.login_name'):session('user_auth.phone');
	}else {
		return $name;
	}
}

/**
 * 根据用户ID获取用户昵称
 * @param  integer $uid 用户ID
 * @return string       用户昵称
 */
function get_nickname($uid = 0){
	if(is_login()){
		return session('user_auth.nick_name');
	}else {
		return $name;
	}
}
/**
 * 前台公共库文件
 * 主要定义前台公共函数库
 */

/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function check_verify($code, $id = 1){
	$verify = new \Think\Verify();
	return $verify->check($code, $id);
}

/**
 * 获取导航URL
 * @param  string $url 导航URL
 * @return string      解析或的url
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_nav_url($url){
    switch ($url) {
        case 'http://' === substr($url, 0, 7):
        case '#' === substr($url, 0, 1):
            break;        
        default:
            $url = U($url);
            break;
    }
    return $url;
}

 

/**
 * 自动登录用户
 * @param  integer $user 用户信息数组
 */
function autoSession($user){

	/* 记录登录SESSION和COOKIES */
	$auth = array(
		'id'              => $user['id'],
		'uid'             => $user['id'],
		'nick_name'       => $user['nick_name'],
		'login_name'      => $user['login_name'],
		'email'       	  => $user['email'],
		'phone'           => $user['phone'],
		'time_zone'       => $user['time_zone'],
		'photoPath'       => $user['photoPath'],
		'last_login_time' => $user['last_login_time'],
	);
	session('user_auth', $auth);
}


// function i_array_column($input, $columnKey, $indexKey=null){
// 	if(!function_exists('array_column')){
// 		$columnKeyIsNumber  = (is_numeric($columnKey))?true:false;
// 		$indexKeyIsNull            = (is_null($indexKey))?true :false;
// 		$indexKeyIsNumber     = (is_numeric($indexKey))?true:false;
// 		$result                         = array();
// 		foreach((array)$input as $key=>$row){
// 			if($columnKeyIsNumber){
// 				$tmp= array_slice($row, $columnKey, 1);
// 				$tmp= (is_array($tmp) && !empty($tmp))?current($tmp):null;
// 			}else{
// 				$tmp= isset($row[$columnKey])?$row[$columnKey]:null;
// 			}
// 			if(!$indexKeyIsNull){
// 				if($indexKeyIsNumber){
// 					$key = array_slice($row, $indexKey, 1);
// 					$key = (is_array($key) && !empty($key))?current($key):null;
// 					$key = is_null($key)?0:$key;
// 				}else{
// 					$key = isset($row[$indexKey])?$row[$indexKey]:0;
// 				}
// 			}
// 			$result[$key] = $tmp;
// 		}
// 		return $result;
// 	}else{
// 		return array_column($input, $columnKey, $indexKey);
// 	}

// }




