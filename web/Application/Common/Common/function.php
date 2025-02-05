<?php

// 常量定义
const VERSION    = '1.0.0.0';

/**
 * 系统公共库文件
 * 主要定义系统公共函数库
 */

//用于测试打印数组数据
function p($arr){
	header('content-type:text/html;charset=utf-8');
	echo '<pre>';
	print_r($arr);
	echo '</pre>';
}
/**
 * 开发新项目时必须先清理之前项目的缓存
 * [get_global_config 获取config表配置]
 * @return [type] [description]
 */
function get_global_config() {
    /* 读取数据库中的配置 */
    $config = S('WA_PAI_CONFIG_DATA');
    if (!$config) {
        $configParse = new \Common\Tools\ConfigParse();
        $config = $configParse->lists();
        S('WA_PAI_CONFIG_DATA', $config);
    }
    C($config); //添加配置


}


/**
  * 用于API调式时输出LOG文件
  * @param mixed $value 要打印的数年据
  * @param string $file 文件要保存的完整路径, 含文件名, 默认在当前控制器目录下同名.log
  * @return null 无返回值
  * by zhaojiping liuniukeji.com
  */
function LL($value='', $file=''){
    if ($file == '') {
        $file = './Application/'. MODULE_NAME .'/Controller/'. CONTROLLER_NAME .'Controller.class.log';
    }
    error_log(print_r($value,1),3, $file);
}

/**
 * 返回JSON通一格式
 * by zhaojiping liuniukeji.com
 */
function V($status=-1, $info='', $data=''){
    if ($status == -1) {
        exit('参数调用错误');
    }
    return array('status'=>$status, 'info'=>$info, 'data'=>$data);
}

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function user_is_login(){
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
 * 调用系统的API接口方法（静态方法）
 * api('User/getName','id=5'); 调用公共模块的User接口的getName方法
 * api('Admin/User/getName','id=5');  调用Admin模块的User接口
 * @param  string  $name 格式 [模块名]/接口名/方法名
 * @param  array|string  $vars 参数
 */
function api($name,$vars=array()){
	$array     = explode('/',$name);
	$method    = array_pop($array);
	$classname = array_pop($array);
	$module    = $array? array_pop($array) : 'Common';
	$callback  = $module.'\\Api\\'.$classname.'Api::'.$method;
	if(is_string($vars)) {
		parse_str($vars,$vars);
	}
	return call_user_func_array($callback,$vars);
}

/**
 * 递归重组数据节点信息为多维数组
 * Array $node 要重组的数组
 * Int $pid 顶级的ID
 * String $pName 父级字段名称
 * by zhaojiping liuniukeji.com
 */
function node_merge($node, $pid = 0, $pName='pid'){
    $arr = array();

    foreach($node as $v){
        if($v[$pName] == $pid){
            $v['child'] = node_merge($node, $v['id']);
            $arr[] = $v;
        }
    }
    return $arr;
}

/**
 * 通用分页处理函数
 * @param Int $count 总条数
 * @param int $page_size 分页大小
 * @return Array  ['page']分页数据  ['limit']查询调用的limit条件
 */
function get_page($count, $page_size=0){
    if ($page_size == 0) $page_size = C('PAGE_SIZE');
	$page = new \Think\Page($count, $page_size);
	$page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
	$show = $page->show();
	$limit = $page->firstRow.','.$page->listRows;
	return array('page'=>$show,'limit'=>$limit);
}

//基于数组创建目录和文件
function create_dir_or_files($files){
	foreach ($files as $key => $value) {
		if(substr($value, -1) == '/'){
			mkdir($value);
		}else{
			@file_put_contents($value, '');
		}
	}
}

/**
 * 通用图片上传函数
 * @param String $imgname 上传文件域的NAME属性
 * @param type $dirname  上传文件存储目录
 * @param type $thumb    需要生成多少个缩略图
 * @return Array
 */
function upload($imgname,$dirname,$thumb=array()){
	if(isset($_FILES[$imgname]) && $_FILES[$imgname]['error'] == 0){
		$upload = new \Think\Upload();
		$rootpath = C('UPLOAD_ROOTPATH');
		$upload->savePath = $rootpath;
		$upload->maxSize = intval(C('IMAGE_MAXSIZE'))*1024*1024;
		$upload->exts = C('ALLOW_IMG_EXT');
		$upload->savePath = $dirname.'/';
		$info = $upload->upload(array($imgname=>$_FILES[$imgname]));
		if(!$info){
			return array('status'=>0,'error'=>$upload->getError());
		}else{
			$ret['status'] = 1;
			$ret['image']['origin'] = $origin_img = $info[$imgname]['savepath'].$info[$imgname]['savename'] ;
			if(is_array($thumb) && !empty($thumb)){
				$image = new \Think\Image();
				foreach($thumb as $k => $v){
					$ret['image']['thumb'][$k] = $info[$imgname]['savepath'].'thumb_'.$k.'_'.$info[$imgname]['savename'];
					$image->open($rootpath.$origin_img);
					$image->thumb($v[0],$v[1])->save($rootpath.$ret['image']['thumb'][$k]);
				}
			}
		}
		return $ret;
	}
}

/**
 * 通用视频上传函数
 * @param String $imgname 上传文件域的NAME属性
 * @param type $dirname  上传文件存储目录
 * @return Array
 */
function upload_video($videoname,$dirname){
	if(isset($_FILES[$videoname]) && $_FILES[$videoname]['error'] == 0){
		$upload = new \Think\Upload();
		$rootpath = C('UPLOAD_ROOTPATH');
		$upload->savePath = $rootpath;
		$upload->maxSize = intval(C('VIDEO_MAXSIZE'))*1024*1024;
		$upload->exts = C('ALLOW_VIDEO_EXT');
		$upload->savePath = $dirname.'/';
		$info = $upload->upload(array($videoname=>$_FILES[$videoname]));
		if(!$info){
			return array('status'=>0,'error'=>$upload->getError());
		}else{
			$ret['status'] = 1;
			$ret['path'] = $origin_img = $info[$videoname]['savepath'].$info[$videoname]['savename'] ;
		}
		return $ret;
	}
}


/**
 * 声音上传函数
 * @param String $imgname 上传文件域的NAME属性
 * @param type $dirname  上传文件存储目录
 * @return Array
 */
function upload_voice($voicename,$dirname){
	if(isset($_FILES[$voicename]) && $_FILES[$voicename]['error'] == 0){
		$upload = new \Think\Upload();
		$rootpath = C('UPLOAD_ROOTPATH');
		$upload->savePath = $rootpath;
		$upload->maxSize = intval(C('VOICE_MAXSIZE'))*1024*1024;
		$upload->exts = C('ALLOW_VOICE_EXT');
		$upload->savePath = $dirname.'/';
		$info = $upload->upload(array($voicename=>$_FILES[$voicename]));
		if(!$info){
			return array('status'=>0,'error'=>$upload->getError());
		}else{
			$ret['status'] = 1;
			$ret['path'] = $origin_img = $info[$voicename]['savepath'].$info[$voicename]['savename'] ;
		}
		return $ret;
	}
}


/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice.'...' : $slice;
}


/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key  加密密钥
 * @param int $expire  过期时间 单位 秒
 * @return string
 */
function ln_encrypt($data, $key = '', $expire = 0) {
    $key  = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data = base64_encode($data);
    $x    = 0;
    $len  = strlen($data);
    $l    = strlen($key);
    $char = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    $str = sprintf('%010d', $expire ? $expire + time():0);

    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1)))%256);
    }
    return str_replace(array('+','/','='),array('-','_',''),base64_encode($str));
}

/**
 * 系统解密方法
 * @param  string $data 要解密的字符串 （必须是ln_encrypt方法加密的字符串）
 * @param  string $key  加密密钥
 * @return string
 */
function ln_decrypt($data, $key = ''){
    $key    = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data   = str_replace(array('-','_'),array('+','/'),$data);
    $mod4   = strlen($data) % 4;
    if ($mod4) {
       $data .= substr('====', $mod4);
    }
    $data   = base64_decode($data);
    $expire = substr($data,0,10);
    $data   = substr($data,10);

    if($expire > 0 && $expire < time()) {
        return '';
    }
    $x      = 0;
    $len    = strlen($data);
    $l      = strlen($key);
    $char   = $str = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1))<ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        }else{
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return base64_decode($str);
}


/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}



/**
 * 获取随机位数数字
 * @param integer $len 长度
 * @return string
 */
function randNumber($len = 4){
    $chars = str_repeat('0123456789', 10);
    $chars = str_shuffle($chars);
    $str = substr($chars, 0, $len);
    return $str;
}

/**
 * 手机格式验证
 * @param string $mobile 验证的手机号码
 * @return boolean
 */
function isMobile($mobile){
	if ( !empty($mobile) ) {
        if( preg_match("/^1[345789]\d{9}$/", $mobile) ){
            return true;
        }
	}
	return false;
}


/**
 * 电子邮箱格式验证
 * @param  string $email 验证的邮件地址
 * @return boolean
 */
function isEmail($email) {
    if ( !empty($email) ){
        if( preg_match('/^[a-z0-9]+([\+_\-\.]?[a-z0-9]+)*@([a-z0-9]+[\-]?[a-z0-9]+\.)+[a-z]{2,6}$/i', $email) ){
            return true;
        }
    }
    return false;
}





/**
 * 判断是否为合法的日期
 * @param string $date 验证的日期
 * @return boolean
 * by zhaojiping <QQ: 17620286>
 */
function validateDate($date) {
    $dateArr = explode("-", $date);
    if (is_numeric($dateArr[0]) === false || is_numeric($dateArr[1]) === false || is_numeric($dateArr[2]) === false) return false;

    if($dateArr[0] > 2050 || $dateArr[0] < 1900) return false;
    if($dateArr[1] > 12 || $dateArr[1] < 1) return false;

    if($dateArr[1] == 2){
        if($dateArr[0] % 100 == 0){
            if($dateArr[0] % 400 == 0){
                if($dateArr[2] > 29 || $dateArr[2] < 0) return false;
            }else{
                if($dateArr[2] > 28 || $dateArr[2] < 0) return false;
            }
        }else{
            if($dateArr[0] % 4 == 0){
                if($dateArr[2] > 29 || $dateArr[2] < 0) return false;
            }else{
                if($dateArr[2] > 28 || $dateArr[2] < 0) return false;
            }
        }
    }
    if($dateArr[1] == 1 || $dateArr[1] == 3 ||$dateArr[1] == 5 ||$dateArr[1] == 7
            ||$dateArr[1] == 8 ||$dateArr[1] == 10 ||$dateArr[1] == 12 ){
        if($dateArr[2] > 31 || $dateArr[2] < 1) return false;
    }else{
        if($dateArr[2] > 30 || $dateArr[2] < 1) return false;
    }
    return true;
}
/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 */
function list_to_tree($list, $pk='id', $pid = 'parent_id', $child = '_child', $root = 0) {
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId =  $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}


/**
 * 时间戳格式化
 * @param int $time
 * @return string 格式化后的时间字符串
 */
function time_format($time = NULL,$style='Y-m-d H:i:s'){
    $time = $time === NULL ? '' : intval($time);
    return date($style, $time);
}

// 获取用户性别
function show_sex($sex) {
    switch ($sex) {
        case 1  : return    '<span class = "sex_0">男</span>';   break;
        case 2  : return    '<span class = "sex_1">女</span>';   break;
        case 0  : return    '<span class = "sex_2">保密</span>';   break;
        default : return    false;      break;
    }
}


/**
 * 获取父节点的所有子节点, 包含父节点本身
 *
 * @param array  $table: 操作的表名
 * @param int    $parent_id: 父节点的ID
 * @param string $childIds: 内部传递ID使用, 外部调用时无需要传递
 * @return array 所有子节点的ID
 * create by zhaojiping QQ:17620286
 */
function getChildIds($table, $parent_id, $childIds = ''){
    if ($childIds == '') $childIds = $parent_id;

    $ids = M($table)->where('parent_id in('. $parent_id .')')->getField('id',true);

    $ids = implode(',', $ids);
    // dump($ids);die;
    //未找到,返回已经找到的
    if ($ids){
        //添加到集合中
        $childIds .= ','. $ids; // 1,2,3,4
        //递归查找
        getChildIds($table, $ids, $childIds);
    }
    return explode(',', $childIds);
}

/**
 * 获取节点的所有父节点, 包含节点本身
 *
 * @param array  $table: 操作的表名
 * @param int    $id: 节点的ID
 * @param string $pIds: 内部传递ID使用, 外部调用时无需要传递
 * @return array 所有父节点的ID
 * create by liuyang  594353482
 */
function getParentIds($table, $id, $pIds = ''){
    if ($pIds == '') $pIds = $id;

    $parent_ids = M($table)->where('id ='. $id)->getField('parent_id');
    //未找到,返回已经找到的
    if ($parent_ids > 0){
        //添加到集合中
        $pIds .= ','. $parent_ids; // 1,2,3,4
        //递归查找
        getParentIds($table, $parent_ids, $pIds);
    }
    return explode(',', $pIds);
}

/**
 * 获取图片缩略图 如果缩略图不存在则生成
 * @param string $filename 要生成缩略图的原图地址
 * @param int $width 生成缩略图的宽度
 * @param int $height 生成缩略图的高度
 * @return mixed 正常返回缩略图的地址
 * create by zhaojpiing QQ: 17620286
 */
function thumb($filename, $width=120, $height=120){
    if ($filename == '') {
        return '';
    }
    $info = pathinfo($filename);

    // 如果图片已经是缩略图, 直接返回
    $thumbFlag = '_' . $width .'_'. $height;
    $thumbFlagLen = strlen($thumbFlag);
    if (substr($info['filename'], -$thumbFlagLen) == $thumbFlag && file_exists($filename)) {
        return '/' . $filename;
    }

    $oldFile = $info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . '.' . $info['extension'];
    $thumbFile = $info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . $thumbFlag .'.' . $info['extension'];

    $oldFile = str_replace('\\', '/', $oldFile);
    $thumbFile = str_replace('\\', '/', $thumbFile);

    $filename = ltrim($filename, '/');
    $oldFile = ltrim($oldFile, '/');
    $thumbFile = ltrim($thumbFile, '/');

    //如果原图不存在, 清除缩略图, 返回原图地址
    if (!file_exists($oldFile)) {
        @unlink($thumbFile);
        return '/' . $oldFile;
    }else if(file_exists($thumbFile)){ //缩图已存在, 直接返回缩略图
        return '/' . $thumbFile;
    }else{ //生成缩略图
        $oldimageinfo = getimagesize($oldFile);
        $old_image_width = intval($oldimageinfo[0]);
        $old_image_height = intval($oldimageinfo[1]);
        if ($old_image_width <= $width && $old_image_height <= $height) {
            @unlink($thumbFile);
            @copy($oldFile, $thumbFile);

            return '/' . $thumbFile;

        } else {
            $image = new \Think\Image();
            if ($old_image_width < $old_image_height) {
                $myHeight = $old_image_height * $width / $old_image_width;
                // 压缩
                $image->open($oldFile)->thumb($width, $myHeight, \Think\Image::IMAGE_THUMB_SCALE)->save($thumbFile, null, 100, false);
            } else {
                $myWidth = $old_image_width * $height / $old_image_height;
                // 压缩
                $image->open($oldFile)->thumb($myWidth, $height, \Think\Image::IMAGE_THUMB_SCALE)->save($thumbFile, null, 100, false);
            }

            if (intval($height) == 0 || intval($width) == 0) {
                exit('/' . $oldFile);
            }
            //dump($image);exit;
            // 再居中截取
            $image->open($thumbFile)->thumb($width, $height, \Think\Image::IMAGE_THUMB_CENTER)->save($thumbFile, null, 95, false);

            //缩图失败
            if (!$image) {
              $thumbFile = $oldFile;
            }

            return '/' . $thumbFile;
        }
    }
}


/**
 * 极光推送通用消息
 * @param unknown $alert  提示标题
 * @param unknown $type 信息类型
 * @param unknown $userId 用户id 可传数组
 * @param unknown $msg  信息内容
 * @param unknown $title  信息标题
*/
function jPush( $alert, $type, $userId = null, $msg = '') {
    require_once ('./Plugins/JPush/JPush.php');
    try {
        $client = new \JPush( C( 'USER_PUSH_APIKEY' ), C( 'USER_PUSH_SECRETKEY' ) );

        $extras = array (
                'type' => $type,
                'alert' => $alert,
                'content' => $msg
        );

        $client = $client->push();
        $client = $client->setPlatform( 'all' );
        $client = $client->setNotificationAlert( $alert );
        $client = $client->addIosNotification( $alert, 'default', null, null, null, $extras );
        //$client = $client->setMessage ( $alert, $alert, 'type', $extras );
        $client = $client->addAndroidNotification( $alert, $alert, null, $extras );
        $client = $client->setOptions( 100000, 3600, null, false ); //测试环境
        //$client = $client->setOptions ( 100000, 3600, null, true ); //生产环境
        if ($userId) {
            // $client = $client->addRegistrationId ( $registrationIds );
            $client->addAlias( $userId );
        } else {
            $client = $client->addAllAudience();
        }

        $result = $client->send();
        // echo 'Result=' . json_encode ( $result ) . $br;
        return json_encode( $result );
    }catch (Exception $e){
        return $e->getMessage();
    }
}
//无限极分类
function getTree($arr,$parent_id=0, &$tree = array()){
    foreach($arr as $key => $value){
        if($value['parent_id'] == $parent_id){
            $tree[] = $value;
            getTree($arr, $value['id'], $tree);
        }
    }

    return $tree;
}

// 获取是否
function show_bool($bool) {
    switch ($bool) {
        case 0  : return    '<span class = "bool_0">是</span>';   break;
        case 1  : return    '<span class = "bool_1">否</span>';   break;
        default : return    false;      break;
    }
}

/**
 * 根据状态生成按钮
 * @param int $id 主键ID
 * @param int $disabled 状态改变前的状态值
 * @return mixed
 * mfy 20161031
 */
function change_disabled($id = '', $disabled = '' ) {
    if ($id == '' || $disabled == '') {
        return '参数错误, 所需参数未传递';
    }
    switch ($disabled){
        case 0 :
            $str = '<a class="tw-tool-btn-stop" href="javascript:void(0)" onclick="javascript:change_disabled(\''. $id .'\', \''. $disabled .'\')" ><i class="tw-icon-crosshairs"></i> 禁用</a>';
            return $str;
            break;
        case 1 :
            $str = '<a class="tw-tool-btn-stop" href="javascript:void(0)" onclick="javascript:change_disabled(\''. $id .'\', \''. $disabled .'\')" ><i class="tw-icon-crosshairs"></i> 启用</a>';
            return $str;
            break;
        default :
            return false;
            break;
    }
}


// 获取用户用户在线状态 0 不在线  1 在线  mfy 20161031
function show_online_status($online_status) {
    switch ($online_status) {
        case 0  : return    '<span class = "bool_1">不在线</span>';   break;
        case 1  : return    '<span class = "bool_0">在线</span>';   break;
        default : return    false;      break;
    }
}

// 获取用户登录方式: 0:第三方,1账号 mfy 20161031
function show_login_type($login_type) {
    switch ($login_type) {
        case 0  : return    '<span class = "bool_1">第三方</span>';   break;
        case 1  : return    '<span class = "bool_0">账号</span>';   break;
        default : return    false;      break;
    }
}


/**
 * 公用模型参数返回函数
 * @return unknown
 *
 */
function  return_status($status, $msg, $data=''){
    $info['status']=$status;
    $info['msg']=$msg;
    $info['data']=$data;
    return $info;
}

function sendMessageRequest($mobile, $content) {
    /********参数配置区域start*********/

    $min_limit = 1; //每分钟限制条数
    $day_limit = 50; //每天短信限制条数
    $sign = '【每天收卡】'; // 企业签名
    $username = 'ln_mtskw'; // 用户名
    $password = 'lnkj123'; // 密码

    /********参数配置区域end*********/

    /**********短信条数限制处理区域start*******/
    $count = S('sms_count_' . date('YmdHi') . $mobile);
    $dayCount = S('sms_count_' . date('Ymd') . $mobile);

    if ($count >= $min_limit) {
        LL($mobile . '短信超出限制,' . date('Y-m-d Hi') . ':' . $count, './logs/sms_privalige_min' . date('Y_m_d') . '.log');
        return V(0, '验证码1分钟内不能重复发送');
    }
    if ($dayCount >= $day_limit) {
        LL($mobile . '短信超出限制,' . date('Y-m-d') . ':' . $dayCount, './logs/sms_privalige_day' . date('Y_m_d') . '.log');
        return V(0, '24小时内不能再发送短信');
    }

    $count || $count = 0;
    $dayCount || $dayCount = 0;
    S('sms_count_' . date('YmdHi') . $mobile, ++$count, 60);
    S('sms_count_' . date('Ymd') . $mobile, ++$dayCount, 60 * 60 * 24);
    /**********短信条数限制处理区域end*******/
    $phone = $mobile; // 目标号码
//    $url = "http://api.ykqxt.com/yksmservice.asmx/SendSMAsArray";
    $url = "http://www.dxcxpt.com:8088/v2sms.aspx";
    //$content = $content; // 短信内容
    $content = urlencode($sign . $content); // 短信内容之后添加企业签名，同时进行UrlEncode转码
    // $pipeid = '';
    $istimer = 'FALSE';
//    $timerset = date('Y-m-d H:i:s', time());
    $timerset = date("YmdHis");
    $sign_p=md5($username.$password.date("YmdHis"));
    $identifyNum = '';

//    $postdata = "username=$username&password=$password&phone=$phone&content=$content&pipeid=&istimer=$istimer&timerset=$timerset&identifyNum=$identifyNum";
    $postdata = "userid=967&timestamp=$timerset&sign=$sign_p&mobile=$phone&content=$content&sendTime=&action=send&extno=";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    $result = curl_exec($ch);
    curl_close($ch);

    // 发送有没有成功
    if (strstr($result, "Success") == false) {
        LL($mobile.'   '.$postdata.'     '.$result, './logs/sms_error' . date('Y_m_d') . '.log');
        return V(0, $result);
    } else {
        LL($mobile.'   '.$postdata.'     '.$result, './logs/sms_success' . date('Y_m_d') . '.log');
        return V(1, '短信发送成功');
    }

}


/**
 * 作用：将xml转为array
 */
function xmlToArray($xml) {
    // 将XML转为array
    libxml_disable_entity_loader(true);
    libxml_use_internal_errors();
    $array_data = json_decode ( json_encode ( simplexml_load_string ( $xml, 'SimpleXMLElement', LIBXML_NOCDATA ) ), true );
    return $array_data;
}
/**
 * +----------------------------------------------------------
 * 生成随机字符串
 * +----------------------------------------------------------
 * @param int $length 要生成的随机字符串长度
 * @param string $type 随机码类型：0，数字+大小写字母；1，数字；2，小写字母；3，大写字母；4，特殊字符；-1，数字+大小写字母+特殊字符
 * +----------------------------------------------------------
 * @return string
+----------------------------------------------------------
 */
function randCode($length = 5, $type = 0) {
    $arr = array(1 => "0123456789", 2 => "abcdefghijklmnopqrstuvwxyz", 3 => "ABCDEFGHIJKLMNOPQRSTUVWXYZ", 4 => "~@#$%^&*(){}[]|");
    if ($type == 0) {
        array_pop($arr);
        $string = implode("", $arr);
    } elseif ($type == "-1") {
        $string = implode("", $arr);
    } else {
        $string = $arr[$type];
    }
    $count = strlen($string) - 1;
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $string[rand(0, $count)];
    }
    return $code;
}

/**
 * 根据状态生成按钮
 * @param int $id 主键ID
 * @param int $is_hot 状态改变前的状态值
 * @return mixed

 */
function change_is_hot($id = '', $is_hot = '') {
    if ($id == '' || $is_hot == '') {
        return '参数错误, 所需参数未传递';
    }
    switch ($is_hot) {
        case 0 :
            $str = '<a class="tw-tool-btn-stop" href="javascript:void(0)" onclick="javascript:change_disabled(\'' . $id . '\', \'' . $is_hot . '\')" ><i class="tw-icon-crosshairs"></i> 设为热门</a>';
            return $str;
            break;
        case 1 :
            $str = '<a class="tw-tool-btn-stop" href="javascript:void(0)" onclick="javascript:change_disabled(\'' . $id . '\', \'' . $is_hot . '\')" ><i class="tw-icon-crosshairs"></i> 取消热门</a>';
            return $str;
            break;
        default :
            return false;
            break;
    }
}

function get_user_id($phone){
    $user_id = M('User')->where('phone='.$phone)->getField('id');
    return $user_id;
}

function get_partner(){
    $partner_list = M('Partner')->getField('id,link,photo_path');
    return $partner_list;
}


/**
 * @param $arr
 * @param $key_name
 * @return array
 * 将数据库中查出的列表以指定的 id 作为数组的键名
 */
function convert_arr_key($arr, $key_name) {
    $arr2 = array();
    foreach ($arr as $key => $val) {
        $arr2[$val[$key_name]] = $val;
    }
    return $arr2;
}

/*
* 获取地区列表
*/
function get_region_list(){
    //获取地址列表 缓存读取
    if(!S('region_list')){
        $region_list = M('region')->select();
        $region_list = convert_arr_key($region_list,'id');
        S('region_list',$region_list, 600);
    }

    return $region_list ? $region_list : S('region_list');
}
/*
 * 获取用户地址列表
 */
function get_user_address_list($user_id){
    $lists = M('user_address')->where(array('user_id'=>$user_id))->select();
    return $lists;
}

/*
 * 获取指定地址信息
 */
function get_user_address_info($user_id,$address_id){
    $data = M('user_address')->where(array('user_id'=>$user_id,'address_id'=>$address_id))->find();
    return $data;
}
/*
 * 获取用户默认收货地址
 */
function get_user_default_address($user_id){
    $data = M('user_address')->where(array('user_id'=>$user_id,'is_default'=>1))->find();
    return $data;
}
/**
 * 检查手机号码格式
 * @param $mobile 手机号码
 */
function check_mobile($mobile) {
    if (preg_match('/1[34578]\d{9}$/', $mobile))
        return true;
    return false;
}


function i_array_column($input, $columnKey, $indexKey = null) {
    if (!function_exists('array_column')) {
        $columnKeyIsNumber = (is_numeric($columnKey)) ? true : false;
        $indexKeyIsNull = (is_null($indexKey)) ? true : false;
        $indexKeyIsNumber = (is_numeric($indexKey)) ? true : false;
        $result = array();
        foreach ((array)$input as $key => $row) {
            if ($columnKeyIsNumber) {
                $tmp = array_slice($row, $columnKey, 1);
                $tmp = (is_array($tmp) && !empty($tmp)) ? current($tmp) : null;
            } else {
                $tmp = isset($row[$columnKey]) ? $row[$columnKey] : null;
            }
            if (!$indexKeyIsNull) {
                if ($indexKeyIsNumber) {
                    $key = array_slice($row, $indexKey, 1);
                    $key = (is_array($key) && !empty($key)) ? current($key) : null;
                    $key = is_null($key) ? 0 : $key;
                } else {
                    $key = isset($row[$indexKey]) ? $row[$indexKey] : 0;
                }
            }
            $result[$key] = $tmp;
        }
        return $result;
    } else {
        return array_column($input, $columnKey, $indexKey);
    }
}


/**
 * 获取指定日期段内每一天的日期
 * @param  Date  $startdate 开始日期
 * @param  Date  $enddate   结束日期
 * @return Array
 */
function getDateFromRange($startdate, $enddate){

    $stimestamp = strtotime($startdate);
    $etimestamp = strtotime($enddate);

    // 计算日期段内有多少天
    $days = ($etimestamp-$stimestamp)/86400+1;

    // 保存每天日期
    $date = array();

    for($i=0; $i<$days; $i++){
        $date[] = date('Y-m-d', $stimestamp+(86400*$i));
    }

    return $date;
}

/**
 * 地址添加/编辑
 * @param $user_id 用户id
 * @param $user_id 地址id(编辑时需传入)
 * @return array
 */
function add_address($user_id,$address_id=0,$data){

    $post = $data;
    if($address_id == 0)
    {
        $c = M('UserAddress')->where("user_id = $user_id")->count();
        if($c >= 20)
            return array('status'=>-1,'msg'=>'最多只能添加20个收货地址','result'=>'');
    }

    //检查手机格式
    if($post['consignee'] == '')
        return array('status'=>-1,'msg'=>'收货人不能为空','result'=>'');
    if(!$post['province'] || !$post['city'] || !$post['district'])
        return array('status'=>-1,'msg'=>'所在地区不能为空','result'=>'');
    if(!$post['address'])
        return array('status'=>-1,'msg'=>'地址不能为空','result'=>'');
    if(!check_mobile($post['mobile']))
        return array('status'=>-1,'msg'=>'手机号码格式有误','result'=>'');
    //编辑模式
    if($address_id > 0){
        $address = M('user_address')->where(array('address_id'=>$address_id,'user_id'=> $user_id))->find();
        if($post['is_default'] == 1 && $address['is_default'] != 1)
            M('user_address')->where(array('user_id'=>$user_id))->save(array('is_default'=>0));
            $row = M('user_address')->where(array('address_id'=>$address_id,'user_id'=> $user_id))->save($post);
        if($row !== false){
            return array('status'=>1,'msg'=>'编辑成功','result'=>'');
        }else{
            return array('status'=>-1,'msg'=>'编辑失败','result'=>'');
        }
    }
    //添加模式
    $post['user_id'] = $user_id;
    // 如果目前只有一个收货地址则改为默认收货地址
    $c = M('user_address')->where("user_id = {$post['user_id']}")->count();
    if($c == 0)  $post['is_default'] = 1;
    $address_id = M('user_address')->add($post);
    //如果设为默认地址
    $insert_id = M()->getLastInsID();
    $map['user_id'] = $user_id;
    $map['address_id'] = array('neq',$insert_id);
    if($post['is_default'] == 1){
        M('user_address')->where($map)->save(array('is_default'=>0));
    }
    if($address_id !== false){
        return array('status'=>1,'msg'=>'添加成功','result'=>$address_id);
    }else{
        return array('status'=>-1,'msg'=>'添加失败','result'=>'');
    }
}



/**
 * 数组转xls格式的excel文件
 * @param  array  $data      需要生成excel文件的数组
 * @param  string $filename  生成的excel文件名
 *      示例数据：
$data = array(
array(NULL, 2010, 2011, 2012),
array('Q1',   12,   15,   21),
array('Q2',   56,   73,   86),
array('Q3',   52,   61,   69),
array('Q4',   30,   32,    0),
);
 * @param  string  $subject    excel主题
 * @param  string  $title    excel标题
 * @param  array  $sheet    需要处理的单元格样式
 * @param  int  $count    excel数据行
 */
function create_xls($data,$filename='ibhotel.xls',$subject='ibhotel',$title='ibhotel',$sheet=array(), $count = 0){
    ini_set('max_execution_time', '0');
    Vendor('PHPExcel.PHPExcel');
    $filename=str_replace('.xls', '', $filename);
    $phpexcel = new PHPExcel();
    $phpexcel->getProperties()
        ->setCreator("admin")
        ->setLastModifiedBy("admin")
        ->setTitle("ibhotel")
        ->setSubject($subject)
        ->setDescription('')
        ->setKeywords($subject)
        ->setCategory("");
    $phpexcel->setActiveSheetIndex(0);
    $phpexcel->getActiveSheet()->freezePane('A2');//冻结首行
    foreach ($sheet as $key => $value) {
        //设置单元格宽度
        $phpexcel->getActiveSheet()->getColumnDimension($value)->setWidth(20);
        //设置标题行字体样式
        $phpexcel->getActiveSheet()->getStyle($value.'1')->getFont()->setName('微软雅黑');
        $phpexcel->getActiveSheet()->getStyle($value.'1')->getFont()->setSize(12);
        $phpexcel->getActiveSheet()->getStyle($value.'1')->getFont()->setBold(true);
    }
    $maxrow = $sheet[count($sheet)-1];
    //设置居中
    $phpexcel->getActiveSheet()->getStyle('A1:'.$maxrow.$count)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    //所有垂直居中
    $phpexcel->getActiveSheet()->getStyle('A1:'.$maxrow.$count)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

    //设置单元格边框
    $phpexcel->getActiveSheet()->getStyle('A1:'.$maxrow.$count)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    //设置单元格格式为文本
    $phpexcel->getActiveSheet()->getStyle('A1:'.$maxrow.$count)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
    //设置自动换行
    $phpexcel->getActiveSheet()->getStyle('A1:'.$maxrow.$count)->getAlignment()->setWrapText(true);

    $phpexcel->getActiveSheet()->fromArray($data);
    $phpexcel->getActiveSheet()->setTitle($title);
    $phpexcel->setActiveSheetIndex(0);
    ob_end_clean();//清除缓冲区,避免乱码
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    //多浏览器下兼容中文标题
    $encoded_filename =  urlencode($filename);
    $ua = $_SERVER["HTTP_USER_AGENT"];
    if (preg_match("/IE/", $ua)) {
        header('Content-Disposition: attachment; filename="' . $encoded_filename . '.xls"');

    } else if (preg_match("/Firefox/", $ua)) {
        header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '.xls"');
    } else {
        header('Content-Disposition: attachment; filename="' . $encoded_filename . '.xls"');

    }
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0
    $objwriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel5');
    $objwriter->save('php://output');
    exit;
}
/**
 * 发送邮件核心模块
 * @param  string $address 需要发送的邮箱地址 发送给多个地址需要写成数组形式
 * @param  string $subject 标题
 * @param  string $content 内容
 * @return boolean       是否成功
 */
function send_email($address,$subject,$content){
    $email_smtp=C('EMAIL_SMTP');
    $email_username=C('EMAIL_USERNAME');
    $email_password=C('EMAIL_PASSWORD');
    $email_from_name=C('EMAIL_FROM_NAME');
    if(empty($email_smtp) || empty($email_username) || empty($email_password) || empty($email_from_name)){
        return array("status"=>0,"message"=>'邮箱配置不完整');
    }
    require './ThinkPHP/Library/Org/Nx/class.phpmailer.php';
    require './ThinkPHP/Library/Org/Nx/class.smtp.php';
    $phpmailer=new \Phpmailer();
    // 设置PHPMailer使用SMTP服务器发送Email
    $phpmailer->IsSMTP();
    // 设置为html格式
    $phpmailer->IsHTML(true);
    // 设置邮件的字符编码'
    $phpmailer->CharSet='UTF-8';
    // 设置SMTP服务器。
    $phpmailer->Host=$email_smtp;
    // 设置为"需要验证"
    $phpmailer->SMTPAuth=true;
    // 设置用户名
    $phpmailer->Username=$email_username;
    // 设置密码
    $phpmailer->Password=$email_password;
    // 设置邮件头的From字段。
    $phpmailer->From=$email_username;
    // 设置发件人名字
    $phpmailer->FromName=$email_from_name;
    // 添加收件人地址，可以多次使用来添加多个收件人
    $phpmailer->SMTPSecure = 'ssl';
    $phpmailer->Port = '465';
    if(is_array($address)){
        foreach($address as $addressv){
            $phpmailer->AddAddress($addressv);
        }
    }else{
        $phpmailer->AddAddress($address);
    }
    // 设置邮件标题
    $phpmailer->Subject=$subject;
    // 设置邮件正文
    $phpmailer->Body=$content;
    // 发送邮件。
    if(!$phpmailer->Send()) {
        $phpmailererror=$phpmailer->ErrorInfo;
        return array("status"=>0,"message"=>$phpmailererror);
    }else{
        return array("status"=>1,'message'=>'邮件已发送，请在10分钟内填写验证码');
    }

}

/**
 * 手机格式验证
 * @param string $mobile 验证的手机号码
 * @return boolean
 */
function is_mobile($mobile) {
    if (!empty($mobile)) {
        return preg_match('/^1[3|4|5|7|8|9][0-9]\d{8}$/', $mobile);
    }
    return false;
}


//针对阿里云的接口添加的
 function postForm($host,$path,$appcode,$bodys){
    $method = "POST";
    $headers = array();
    array_push($headers, "Authorization:APPCODE " . $appcode);
    //根据API的要求，定义相对应的Content-Type
    array_push($headers, "Content-Type".":"."application/x-www-form-urlencoded; charset=UTF-8");
    $querys = "";
    $url = $host . $path;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_FAILONERROR, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    if (1 == strpos("$".$host, "https://"))
    {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    }
    curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
    return curl_exec($curl);

}

 function getForm($host,$path,$appcode,$querys){
    $method = "GET";
    $headers = array();
    array_push($headers, "Authorization:APPCODE " . $appcode);
    $bodys = "";
    $url = $host . $path . "?" . $querys;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_FAILONERROR, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    if (1 == strpos("$".$host, "https://"))
    {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    }
    $api_result= curl_exec($curl);
    curl_close($curl);
    return $api_result;

}
/**
 *   实现中文字串截取无乱码的方法
 */
function getSubstr($string, $start, $length) {
    if(mb_strlen($string,'utf-8')>$length){
        $str = mb_substr($string, $start, $length,'utf-8');
        return $str.'****';
    }else{
        return $string;
    }
}
function getSubstr_s($string, $start, $length) {
    if(mb_strlen($string,'utf-8')>$length){
        $str = mb_substr($string, $start, mb_strlen($string,'utf-8')-$length,'utf-8');
        return $str.'****';
    }else{
        return $string;
    }
}



//针对某一个键值来进行去重
 function second_array_unique_bykey($arr, $key){
    $tmp_arr = array();
    foreach($arr as $k => $v)
    {
        if(in_array($v[$key], $tmp_arr))  //搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true
        {
            unset($arr[$k]); //销毁一个变量 如果$tmp_arr中已存在相同的值就删除该值
        }else {
            $tmp_arr[$k] = $v[$key]; //将不同的值放在该数组中保存
        }
    }
    //ksort($arr); //ksort函数对数组进行排序(保留原键值key) sort为不保留key值
    return $arr;
}
///二维数组内部的一维数组中的值不能完全相同，删除其中重复的项
 function more_array_unique($arr=array()){
    foreach ($arr[0] as $k => $v) {
        $arr_inner_key[] = $k; //先把二维数组中的内层数组的键值记录在在一维数组中
    }
    foreach ($arr as $k => $v) {
        $v = join(",", $v); //降维 用implode()也行
        $temp[$k] = $v; //保留原来的键值 $temp[]即为不保留原来键值
    }
    // printf("After split the array:<br>");
    // print_r($temp);//输出拆分后的数组
    // echo "<br/>";
    $temp = array_unique($temp); //去重：去掉重复的字符串
    foreach ($temp as $k => $v) {
        $a = explode(",", $v); //拆分后的重组 如：Array( [0] => james [1] => 30 )
        $arr_after[$k] = array_combine($arr_inner_key, $a); //将原来的键与值重新合并
    }
    //ksort($arr_after);//排序如需要：ksort对数组进行排序(保留原键值key) ,sort为不保留key值
    return $arr_after;
}











