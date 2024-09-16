<?php
namespace Home\Controller;

use Think\Controller;

/**
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 */
class HomeCommonController extends Controller {

    /* 空操作，用于输出404页面 */
   /* public function _empty() {
        $this->display();
    }*/


    protected function _initialize() {
        //define('UID', user_is_login()['id']);
        define('UID', user_is_login()['id']);
//        define('UID', 29);
        if( !UID ){// 还没登录 跳转到登录页面
            //$this->redirect('/Member/Public/login');
            $this->assign('is_login',3);
        }
        $this->assign('userInfo',session('user_auth'));
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if(go_mobile($user_agent)){
            $url = "http://m.10.168.1.254/";
            header('Location:'.$url);
        }
        /* 读取站点配置 */
        $config = api('Config/lists');
        C($config); //添加配置
    }

    /* 用户登录检测 */
    protected function login() {
        /* 用户登录检测 */
        is_login() || $this->error('您还没有登录，请先登录！', U('User/login'));
    }

}
