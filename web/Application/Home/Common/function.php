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
		'username'      => $user['username'],
		'phone'           => $user['phone'],
        'password'      => $user['password'],
	);
	session('user_auth', $auth);
}


    function isExits($mobile){
        $where['phone'] = array('eq',$mobile);
        $count = M('Member')->where($where)->count();
        if($count==0){
            return false;
        }
    }

    function go_mobile($user_agent){
	    $clientkeywords = array ('nokia',
            'sony',
            'ericsson',
            'mot',
            'samsung',
            'htc',
            'sgh',
            'lg',
            'sharp',
            'sie-',
            'philips',
            'panasonic',
            'alcatel',
            'lenovo',
            'iphone',
            'ipod',
            'blackberry',
            'meizu',
            'android',
            'netfront',
            'symbian',
            'ucweb',
            'palm',
            'operamobi',
            'openwave',
            'nexusone',
            'cldc',
            'midp',
            'wap',
            'mobile',
            'comFront',
            'Windows Phone',
            'Windows CE',
            'Opera Mini',

    	);

    	// 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))){
            return true;
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
    }


function _uploadImg(){
	$oldImg = I('oldImg', '', 'htmlspecialchars');
	$savePath = I('savePath', '', 'htmlspecialchars');

	if($savePath != '') $savePath = $savePath . '/';

	$result = array( 'status' => 1, 'msg' => '上传完成');
	//判断有没有上传图片
	//p(trim($_FILES['photo2']['name']));
	if(trim($_FILES['photo']['name']) != ''){
		$upload = new \Think\Upload(C('PICTURE_UPLOAD')); // 实例化上传类
		$upload->replace  = true; //覆盖
		$upload->savePath = $savePath; //定义上传目录
		//如果有上传名, 用原来的名字
		if($oldImg != '') $upload->saveName = $oldImg;
		// 上传文件
		$info = $upload->uploadOne($_FILES['photo']);
		if(!$info) {
			$result = array( 'status' => 0, 'msg' => $upload->getError() );
		}else{
			if ($oldImg != '') {
				//删除缩略图
				$dir = '.'.C('UPLOAD_PICTURE_ROOT') . '/' . $info['savepath'];
				$filesnames = scandir($dir);
				foreach ($filesnames as $key => $value) {
					if ($value === '.' || $value === '..') {
						continue;
					}
					$count = strpos($value, $oldImg.'_');
					if ($count !== false) {
						$file = '.' . __ROOT__ . C('UPLOAD_PICTURE_ROOT') . '/' . $info['savepath'].$value;
						if (file_exists($file) == true) {
							@unlink($file);
						}
					}
				}
			}
			$result['src'] = C('UPLOAD_PICTURE_ROOT') . '/' . $info['savepath'] . $info['savename'];
		}
		$this->ajaxReturn($result);
	}
}
function object_array($array) {
    if(is_object($array)) {
        $array = (array)$array;
     } if(is_array($array)) {
         foreach($array as $key=>$value) {
             $array[$key] = object_array($value);
             }
     }
     return $array;
}
/**
 * 通用分页处理函数
 * @param Int $count 总条数
 * @param int $page_size 分页大小
 * @return Array  ['page']分页数据  ['limit']查询调用的limit条件
 */
function get_page_new($count, $page_size=0){
    if ($page_size == 0) $page_size = C('PAGE_SIZE');
    $page = new \Think\Page($count, $page_size);
    $page->setConfig('next','下一页');
    $page->setConfig('prev','上一页');
    $page->setConfig('theme', '%FIRST% %UP_PAGE%  <span class="linkpage">%LINK_PAGE%</span>  %DOWN_PAGE% %END%');

    $show = $page->show();
    $limit = $page->firstRow.','.$page->listRows;
    return array('page'=>$show,'limit'=>$limit);
}