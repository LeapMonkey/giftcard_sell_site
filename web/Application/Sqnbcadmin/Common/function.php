<?php

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_login(){
	$admin = session('admin_auth');
	if (empty($admin)) {
		return 0;
	} else {
		return $admin;
	}
}

/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 */
function check_verify($code, $id = 1){
	$verify = new \Think\Verify();
	return $verify->check($code, $id);
}

// 获取数据的状态操作 mfy 20161031
function show_status($status) {
    switch ($status) {
        case 0  : return    '<span class = "status_0">禁用</span>';     break;
		case 1  : return    '<span class = "status_0">正常</span>';     break;
        case 2  : return    '<span class = "status_1">已删除</span>';   break;
        default : return    false;      break;
    }
}
// 获取数据的状态操作
function show_disabled($disabled) {
    switch ($disabled) {
        case 1  : return    '<span class = "disabled_1">正常</span>';   break;
        case 0  : return    '<span class = "disabled_0">禁用</span>';   break;
        default : return    false;      break;
    }
}
// 获取数据的状态操作
function show_hide($hide) {
    switch ($hide) {
        case 0  : return    '<span class = "hide_0">是</span>';   break;
        case 1  : return    '<span class = "hide_1">否</span>';   break;
        default : return    false;      break;
    }
}

// 获取数据的状态操作
function show_treat($hide) {
    switch ($hide) {
        case 0  : return    '<span class = "hide_0">未处理</span>';   break;
        case 1  : return    '<span class = "hide_1">已处理</span>';   break;
        default : return    false;      break;
    }
}

// 分析枚举类型配置值 格式 a:名称1,b:名称2
function parse_config_attr($string) {
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if(strpos($string,':')){
        $value  =   array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k]   = $v;
        }
    }else{
        $value  =   $array;
    }
    return $value;
}

/**
 * 获取配置的类型
 * @param string $type 配置类型
 * @return string
 */
function get_config_type($type = 0){
    $list = C('CONFIG_TYPE_LIST');
    return $list[$type];
}

/**
 * 获取配置的分组
 * @param string $group 配置分组
 * @return string
 */
function get_config_group($group = 0){
    $list = C('CONFIG_GROUP_LIST');
    return $group?$list[$group]:'';
}

function int_to_string(&$data,$map=array('status'=>array(1=>'正常',-1=>'删除',0=>'禁用',2=>'未审核',3=>'草稿'))) {
    if($data === false || $data === null ){
        return $data;
    }
    $data = (array)$data;
    foreach ($data as $key => $row){
        foreach ($map as $col=>$pair){
            if(isset($row[$col]) && isset($pair[$row[$col]])){
                $data[$key][$col.'_text'] = $pair[$row[$col]];
            }
        }
    }
    return $data;
}

/**
 * 增加日志
 * @param $log
 * @param bool $name
 */

function addlog($log, $name = false)
{
    $Model = M('AdminLog');
  
    $data['admin_id'] = UID;
   
    $data['operate_time'] = time();
    $data['ip'] = $_SERVER["REMOTE_ADDR"];
    $data['module'] = $log;
    $data['admin_phone'] = M('Admin')->where('id='.UID)->getField('phone');
    $Model->data($data)->add();
}

