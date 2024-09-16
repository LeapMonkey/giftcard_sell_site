<?php
namespace Home\Controller;
Use Think\Log;
use Think\Verify;

/**
 *  前台首页控制器
 * @author guolt
 *
 */
class LoginController extends HomeCommonController {

    /**
     * qq第三方登录
     */
    public function qq()
    {
        $access_token = I('access_token', '');
        if (!$access_token){
            $this->redirect('Index/index');
        }
        $expires_in = I('expires_in', '');
        $graph_url = "https://graph.qq.com/oauth2.0/me?access_token=$access_token";
        $str = file_get_contents($graph_url);
        p($str);
        if (strpos($str, "callback") !== false) {
            $lpos = strpos($str, "(");
            $rpos = strrpos($str, ")");
            $str = substr($str, $lpos + 1, $rpos - $lpos - 1);
            $msg = json_decode($str, true);
        }

        $OpenID = $msg['openid'];$unionid = $msg['unionid'];
        $url2 = "https://graph.qq.com/user/get_user_info?access_token=" . $access_token . '&oauth_consumer_key=101578620' . '&openid=' . $OpenID;
        $res = file_get_contents($url2);//获取个人信息
        $memberInfo = json_decode($res, true);
        $time = time();
        $user = array(
            'qq_openid' => $OpenID,
            'qq_usernick' => $memberInfo['nickname'],
            'qq_avatar_thumb' => $memberInfo['figureurl_qq_1'],
            'username' => $memberInfo['nickname'],
        );
       
        $where['qq_openid'] = $OpenID;
        $users = M('User');
        $info = $users->where($where)->find();


        if ($info == '') {
            $userauth = session('user_auth');
            if (!$userauth) {
                $user['add_time'] = time();
                $res = $users->add($user);
                $userInfo = $users->where('id=' . $res)->field('id,qq_usernick,qq_avatar_thumb')->find();
            } else {
                $user_id = $userauth['id'];
                $users->where('id=' . $user_id)->save($user);
                $userInfo = $users->where('id=' . $user_id)->field('id,qq_usernick,qq_avatar_thumb')->find();
            }

        } else {

            $userInfo = $info;

        }

        $userInfo['username'] = $userInfo['qq_usernick'];
        session('user_auth', null);
        session('user_auth', $userInfo);
        $this->redirect('Home/Index/index');
    }
}
