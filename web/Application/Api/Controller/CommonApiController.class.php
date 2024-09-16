<?php
namespace Api\Controller;
use Think\Controller;
/**
 * 用户登录后, 需要继承的基类
 * create by zhengnian
 */
class CommonApiController extends \Common\Controller\CommonController {
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

//        $uid='';
//        $token=I('token','');
//        if($token){
//            $uid = $this->checkTokenAndGetUid($token);
//        }
//        if ($uid) {
//            define('USERID', $uid);
//        }
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

    /**
     *
     * @param null $msg
     *          返回正确的提示信息
     * @param
     *          status success CURD 操作成功
     * @param array $data
     *          具体返回信息
     *          Function descript: 返回带参数，标志信息，提示信息的json 数组
     *          
     */
    public function returnApiSuccess($msg = null, $data = array()) {
            $result = array (
                            'status' => "1",
                            'info' => $msg,
                            'result' => $data 
            );
            $this->ajaxReturn ( $result );
    }

    public function returnApiError($msg = null) {
            $result = array (
                            'status' => "0",
                            'info' => $msg,
            );
            $this->ajaxReturn ( $result );
    }

    private function checkTokenAndGetUid($token){
        $where['token'] = $token;
        $where['status'] = 0;
        $id = M('Weborle')->where($where)->getField('id');
        return $id;
    }

    public function get_api_page($count,$rows = 10) {
        $_GET['p'] = I('p',1);
        $page = new \Think\Page($count, $rows);
        $limit = $page->firstRow . ',' . $page->listRows;
        return $limit;
    }






















}
