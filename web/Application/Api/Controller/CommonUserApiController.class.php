<?php
namespace Api\Controller;
use Think\Controller;
/**
 * 用户登录后, 需要继承的基类
 * create by zhanglili
 */
class CommonUserApiController extends \Common\Controller\CommonController {
    protected function _initialize(){
        header('Access-Control-Allow-Origin:*');
        // 响应类型
        header('Access-Control-Allow-Methods:POST');
        // 响应头设置
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
        get_global_config(); // 读取配置
        /* 读取数据库中的配置 */
        $config =   S('DB_CONFIG_DATA');
        if(!$config){
            $config = api('Config/lists');
            S('DB_CONFIG_DATA',$config);
        }
        C($config); //添加配置

        $token = I('post.token', '');
        // 判断token值是否正确并返回用户信息
        $uid = $this->checkTokenAndGetUid($token);
        if ($uid > 0) {
            define('UID', $uid);
        } else {
            $this->ajaxReturn(V('0', '登录失效'));
        }
    }

    protected function apiReturn($status, $message='', $data=''){
        if ($status != 0 && $status != 1) {
            exit('参数调用错误 status');
        }

        if ($data != '' && C('APP_DATA_ENCODE') == true) {
            $data = json_encode($data); // 数组转为json字符串
            $aes = new \Common\Tools\Aes();
            $data = $aes->aes128cbcEncrypt($data); // 加密
        }

        if (is_null($data) || empty($data)) $data = array();
        $this->ajaxReturn(V($status, $message, $data));

    }

    private function checkTokenAndGetUid($token){
        $where['token'] = $token;
        $where['status'] = 0;
        $id = M('User')->where($where)->getField('id');
        return $id;
    }



















}
