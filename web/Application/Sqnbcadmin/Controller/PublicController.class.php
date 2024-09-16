<?php
namespace Sqnbcadmin\Controller;
/**
 *  后台首页控制器
 */
class PublicController extends \Think\Controller {

    /**
     * 后台用户登录显示
     */
    public function login(){
        // 123456的加密758e001c4fd2b221540ef0a36a133f02d93a5def7511da3d0f2d171d9c344e91
        if(is_login()){
            $this->redirect('Index/index');
        }else{
            /* 读取数据库中的配置 */
            $config	=	S('DB_CONFIG_DATA');
            if(!$config){
                $config = api('Config/lists');
                S('DB_CONFIG_DATA',$config);
            }
            C($config); //添加配置

            $this->display();
        }

    }

    /**
     * 后台用户登录
     */
    public function doLogin(){
        //$this->ajaxReturn(V(1, '登录成功'));

        /* 检测验证码 TODO: */
        /*   if(!check_verify($verify)){
                $this->error('验证码输入错误！');
            }  */

        $username = I('username', '');
        $password = I('password', '');
        // 获取security_code, 重新组织密码


        $admin = D('Admin');



        $loginInfo = $admin->login($username, $password);
        if( $loginInfo['status'] == 1 ){ //登录成功
            /* 存入session */
            $this->autoSession($loginInfo['data']);

            $this->ajaxReturn(V(1, '登录成功'));
        } else {
            $this->ajaxReturn(V(0, $loginInfo['info']));
        }
    }

    /* 退出登录 */
    public function logout(){
        session(null);
        $this->redirect('login');
    }

    public function verify(){
        $verify = new \Think\Verify();
        $verify->entry(1);
    }

    /**
     * 自动登录用户
     * @param  integer $user 用户信息数组
     */
    private function autoSession($user){
    	/* 记录登录SESSION和COOKIES */
    	$auth = array(
			'id'              => $user['id'],
			'uid'             => $user['id'],
			'username'        => $user['username'],
			'nick_name'       => $user['nick_name'],
			'phone'           => $user['phone'],
			'email'       => $user['email'],
			'last_login_time' => $user['last_login_time']
    	   );
    	session('admin_auth', $auth);
    }


}
