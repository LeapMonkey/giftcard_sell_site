<?php
namespace Home\Controller;

/**
 * 前台首页控制器
 */
class IndexController extends HomeCommonController {
    public function test(){
        $val='4545';
        $cardkeyarray=explode(" ",$val);
        p($cardkeyarray[0]);p($cardkeyarray[1]);
    }


    public function index(){
        $cardtype = M('CardType');

        $cardtype_info = $cardtype->select();

        $announcement=M('Announcement');
        $list=$announcement->order('id desc')->find();
        $canModel=M('CanDo');
        $cando=$canModel->order('id asc')->select();
        $this->assign('cando',$cando);
        $this->assign('list',$list);
        $this->assign('cardtype_info',$cardtype_info);

        $this->display();
    }

     //微信登录
    public function webchatLogin(){
        $code = I('code','');
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx6353058509cb4eda&secret=4cda8f7f3b33570db03ac4ea41655752&code=".$code."&grant_type=authorization_code";

        $res = file_get_contents($url);//获取token等数据{"access_token":"dbERFaeRnx9_fj1UoZq2fIILakAnoOOQ15hXY9BEEEQr5TmmDCDgA0JeZs4helmx4EafHLQ5x4pyI3S1DSU6n-1P5zyfhonFcfru9iePpBw","expires_in":7200,"refresh_token":"AzgntVNMOao16gT2BwmQrAo-Xl9sQ2xxyB1kRUverPLcldpM9VrYMSdL6P2lqKaODwRr4AvhmOmUXPn82DQbGykTQDJUxdcRfX-n0_VSrMM","openid":"ob-3d1bC80TY9recz1zq3B-GV7N8","scope":"snsapi_login","unionid":"owOtgv_EoX4PPWiyMNIAu_OjIFmU"}
        $resStd = json_decode($res);//数据格式stdClass Object
        //将std格式的数据转换成纯数组
        $resArray = object_array($resStd);
        //刷新或续期access_token使用
        $urlRefresh = "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=wx6353058509cb4eda&grant_type=refresh_token&refresh_token=".$resArray['refresh_token'];

        $newToken = file_get_contents($urlRefresh);
        $newTokenStd = json_decode($newToken);//数据格式stdClass Object
        //将std格式的数据转换成纯数组
        $newTokenArray = object_array($newTokenStd);
        //最后获取用户的个人信息
        $urlUserInfo = "https://api.weixin.qq.com/sns/userinfo?access_token=".$newTokenArray['access_token']."&openid=".$newTokenArray['openid'];
        $userInfo = file_get_contents($urlUserInfo);
        $userInfoStd = json_decode($userInfo);//数据格式stdClass Object
        //将std格式的数据转换成纯数组
        $userInfoArray = object_array($userInfoStd);


        //判断用户是否存在，如果存在就是登录，如果不存在，就按照注册然后再跳转
        $where['wx_unionid'] = $userInfoArray['unionid'];
        $where['status'] = 0;
        $info = M('User')->where($where)->field()->find();

       // p($userInfoArray);die;
        if($info){
            //跳转到首页，同时存cookie
            session('user_auth',null);
            session('user_auth',$info);
            $this->redirect('Home/Index/index');
        }else{
            $data['wx_unionid'] = $userInfoArray['unionid'];//微信的唯一标识
            $data['wx_usernick'] = $userInfoArray['nickname'];
            $data['qq_avatar_thumb'] = $userInfoArray['headimgurl'];
            $data['username'] = $userInfoArray['nickname'];
           
            $userauth = session('user_auth');

              if(!$userauth){
                $data['add_time'] = time();
                $res = M('User')->add($data);
                $userInfoWeb = M('User')->where('id='.$res)->field()->find();
              }else{
                $user_id = $userauth['id'];
                M('User')->where('id='.$user_id)->save($data);
                $userInfoWeb = M('User')->where('id='.$user_id)->find();
              }


             $userInfoWeb['username']=  $userInfoWeb['wx_usernick'];
            session('user_auth',null);
            session('user_auth',$userInfoWeb);
            $this->redirect('Home/Index/index');

        }


    }


    public function ajaxUrl(){
        $access_token = I('access_token','');
        $expires_in = I('expires_in','');
        $graph_url = "https://graph.qq.com/oauth2.0/me?access_token=".$access_token;
        $str  = file_get_contents($graph_url);
        if (strpos($str, "callback") !== false){
            $lpos = strpos($str, "(");
            $rpos = strrpos($str, ")");
            $str  = substr($str, $lpos + 1, $rpos - $lpos -1);
            $msg = json_decode($str,true);
         }

        $OpenID = $msg['openid'];

        $url2 = "https://graph.qq.com/user/get_user_info?access_token=".$access_token.'&oauth_consumer_key=101483760'.'&openid='.$OpenID;
        $res = file_get_contents($url2);//获取个人信息
        $memberInfo = json_decode($res,true);
        $time = time();
        $user = array(
                'qq_openid'=>$OpenID,
                'qq_usernick'=>$memberInfo['nickname'],
                'qq_avatar_thumb'=>$memberInfo['figureurl_qq_1'],
                'username'=>$memberInfo['nickname'],

            );

        p(121332);
     
        p($expires_in);
         p($access_token);
p(   $user);die;
        $where['qq_openid'] = $OpenID;
        $users = M('User');
        $info = $users->where($where)->find();


        if($info==''){
             $userauth = session('user_auth');
              if(!$userauth){
                $user['add_time'] = time();
                $res = $users->add($user);
                $userInfo = $users->where('id='.$res)->field('id,qq_usernick,qq_avatar_thumb')->find();
              }else{
                $user_id = $userauth['id'];
                $users->where('id='.$user_id)->save($user);
                $userInfo = $users->where('id='.$user_id)->field('id,qq_usernick,qq_avatar_thumb')->find();
              }

        }else{

            $userInfo = $info;

        }

        $userInfo['username']=  $userInfo['qq_usernick'];
        session('user_auth',null);
        session('user_auth',$userInfo);
        $this->redirect('Home/Index/index');

    }
}