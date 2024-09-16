<?php
namespace Mobile\Controller;

use Think\Controller;

/**
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 */
class HomeCommonController extends Controller {

    protected function _empty( ) {
        $this->display();
    }
 

    protected function _initialize(){
        /* 读取站点配置 */
        $config = api('Config/lists');
        C($config); //添加配置

        $user = session('user_auth');
        $user_info = cookie('user_info');
        if (empty($user)){
            if($user_info){
                    session('user_auth',$user_info);
                    session('userId',$user_info['id']);
                    $user = session('user_auth');
                }
         }
     /*    if(!$user){
        //  $this->display('login');
          //  $this->redirect('Mobile/Public/login');
        }*/
      /*  $userauth = session('user_auth');
        if(!$userauth){
        //  $this->display('login');
            $this->redirect('Mobile/User/login');
        }*/
        

    }

    /* 用户登录检测 */
    protected function login(){
        /* 用户登录检测 */
        is_login() || $this->error('您还没有登录，请先登录！', U('User/login'));
    }

    

}
