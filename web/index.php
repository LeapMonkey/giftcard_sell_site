<?php
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

/**
 * 系统调试设置
 * 项目正式部署后请设置为false
 */
define ( 'APP_DEBUG', true );

// APP 目录名称
define ('APP_NAME', 'Application');

/**
 * 应用目录设置
 * 安全期间，建议安装调试完成后移动到非WEB目录
 */
define ( 'APP_PATH', './Application/' );

/**
 * 缓存目录设置
 * 此目录必须可写，建议移动到非WEB目录
 */
define ( 'RUNTIME_PATH', './Runtime/');
define('SITE_URL','http://'.$_SERVER['HTTP_HOST']);
//防SQL注入
//不需要过滤注入方法验证
$paths = $_SERVER['PHP_SELF'];
$nopass = array('alipayNotify', 'Wnotify', 'WxLogin','edit');
$check = true;
foreach ($nopass as $va) {
    if (stripos($paths, $va) == true) {
        $check = false;
        break;
    }
}
if ($check) {
    //判断是否含有注入并跳出
    function sqlInj($value) {
        $arr = explode('|', 'UPDATEXML|UPDATE|WHERE|EXEC|INSERT|SELECT|DELETE|COUNT|CHR|MID|MASTER|TRUNCATE|DECLARE|BIND|DROP|CREATE|EXP| OR |XOR|LIKE|NOTLIKE|NOT BETWEEN|NOTBETWEEN|BETWEEN|NOTIN|NOT IN|CONTACT|EXTRACTVALUE|INFORMATION_SCHEMA|%20|0x|into|union|user_balance');

        if (is_string($value)) {
            foreach ($arr as $a) {
                if (stripos($value, $a) !== false) exit(json_encode(array('status' => -1, 'info' => '参数错误，含有敏感字符', 'data' => array($a)), 0));
            }
        } elseif (is_array($value)) {
            foreach ($value as $v) {
                //二维数组直接跳出
                if (is_array($v)) exit(json_encode(array('status' => -1, 'info' => '参数错误，含有敏感字符', 'data' => array()), 0));

                foreach ($arr as $a) {
                    if (stripos($v, $a) !== false) exit(json_encode(array('status' => -1, 'info' => '参数错误，含有敏感字符', 'data' => array($a)), 0));
                }
            }
        }
    }

    //过滤请求参数
    foreach ($_REQUEST as $key => $value) {
        sqlInj($value);
    }
}
/**
 * 引入核心入口
 * ThinkPHP亦可移动到WEB以外的目录
 */
require './ThinkPHP/ThinkPHP.php';