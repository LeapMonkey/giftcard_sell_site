<?php

namespace Api\Controller;
/**
 * Copyright (c) 山东六牛网络科技有限公司 https://liuniukeji.com
 *
 * @Description
 * @Author         zhanglili  qq:1640054073
 * @Copyright      Copyright (c) 山东六牛网络科技有限公司 保留所有版权(https://www.liuniukeji.com)
 * @Date           2019.4.23
 * @CreateBy       PhpStorm
 */
class PublicApiController extends CommonApiController
{
    //登录
    public function login(){
        $phone = I('phone');
        $password = I('password');
        $user = M('User');
        if($phone==''){
            $this->apiReturn(0, '请输入账号');
        }
        if(password==''){
            $this->apiReturn(0, '请输入密码');
        }
        $where_login['disabled']=0;
        $where_login['phone|email|login_account']=$phone;
        $check_phone = $user->where($where_login)->count();

        if(!($check_phone > 0)){
            $this->ajaxReturn(V(0, '该手机号未注册'));
        }

        $condition['phone|email|login_account'] = $phone;
        $condition['password'] = md5($password);
        $result = $user->where($condition)->count();
        if($result > 0){
            $where_us['phone|email|login_account']=$phone;
            $userInfo = $user->field('id,username,name,phone,token')->where($where_us)->find();
            $token = $this->getToken($userInfo['id']);
            $data_info['token']= $token;
            $where_user['id']=$userInfo['id'];
            $user->where($where_user)->save($data_info);
            $userInfo['token'] = $token;
            //记录操作日志
            $log['user_id'] = $userInfo['id'];
            $log['type'] = 1;
            $log['add_time'] = time();
            $log['ip'] = get_client_ip();
            M('ActionLog')->data($log)->add();


            $this->ajaxReturn(V(1, '登录成功',$userInfo));

        }else{
            //$this->apiReturn(0, '密码输入不正确');
            $this->ajaxReturn(V(0, '密码输入不正确'));
        }
    }

    //注册
    public function register(){
        $user = M('User');
        //$code = session('phone_code');
        $password = I('password');
        $password_qr = I('password_qr');
        $phone = I('phone');
        $mobile_code = I('mobile_code');
//        if($mobile_code != $code){
//            $this->ajaxReturn(V(0, '验证码输入不正确'));
//        }
        $re_code = $this->checkPhoneVerify($mobile_code,$phone);
        // p($re_code);die;
        if (!$re_code) {
            $this->apiReturn(0, '验证码错误或已过期！');
        }
        if($password==''||$password_qr==''||$phone==''||$mobile_code==''){
            $this->ajaxReturn(V(0, '信息填写不完整'));
        }
        $count = $user->where('phone='.$phone)->count();
        if($count > 0){
           //$this->apiReturn( '该手机号码已经注册');
            $this->ajaxReturn(V(0, '该手机号码已经注册'));
        }
        if($password_qr!=$password){
            $this->ajaxReturn(V(0, '两次输入的密码不一致'));
        }
        $data['username'] = $phone;
        $data['phone'] = $phone;
        $data['password'] = md5($password);
        $data['add_time'] = time();
        $rest = $user->add($data);
        if($rest !==false){
            $token = $this->getToken($rest);
            $data['token'] = $user->where(array('id' => $rest))->save(array('token' => $token));
            //$this->apiReturn('注册成功',$data);
            $this->ajaxReturn(V(1, '注册成功',$data));
        }else{

            $this->ajaxReturn(V(0, '注册失败'));
        }
    }
    //生成token的操作
    public function getToken($uid)
    {
        //$randStr = getRandStr(8);
        //$randNum = randNumber(8);
        // $token = time() . $uid . $randStr . $randNum;
        $token = time().randNumber(18);
        return $token;
    }

    /**
     * 获取短信接口
     */
    public function smsCode() {

        $mobile = I('phone', '');
        $type = I('type', 0, 'intval');
        //1注册短信，2找回密码 3修改密码 4修改手机 5修改提现密码 6设置支付密码
        $type_array = array(1, 2 , 3, 4, 5, 6);
        if (!in_array($type, $type_array)) {
            $this->ajaxReturn(V(0, '参数错误'));
        }

        if (empty($mobile) || !isMobile($mobile)) {
            $this->ajaxReturn(V(0, '请输入有效的手机号码'));
            exit;
        }
        //验证手机号码是否已经验证
        $result = D('Common/Member')->checkMemberExist($mobile);
        if ($result == true && $type == 1) {
            $this->ajaxReturn(V(0, '手机号码已存在'));
        } elseif ($result == false && in_array($type, array(2,3,5,6))) {
            $this->ajaxReturn(V(0, '手机号码不存在'));
        }
// elseif ($result == true && $type == 4) {
//            $this->ajaxReturn(V(0, '手机号码已存在'));
//        }
        // 短信内容
        $sms_code = randCode(C('SMS_CODE_LEN'), 1);
        switch ($type) {
            case 1: //注册短信
                $msg = '注册验证码';
                $sms_content = C('SMS_REGISTER_MSG') . $sms_code;
                break;
            case 2: //找回密码
                $msg = '找回密码验证码';
                $sms_content = C('SMS_FINDPWD_MSG') . $sms_code;
                break;
            case 3: //修改密码
                $msg = '修改密码验证码';
                $sms_content = C('SMS_MODPWD_MSG') . $sms_code;
                break;
            case 4: //修改手机号码
                $msg = '修改手机号验证码';
                $sms_content = C('SMS_MODMOBILE_MSG') . $sms_code;
                break;
            case 5: //解绑手机号
                $msg = '解绑手机号验证码';
                $sms_content = C('SMS_DELMOBILE_MSG') . $sms_code;
                break;
            case 6: //设置支付密码
                $msg = '设置支付密码验证码';
                $sms_content = C('SMS_PAY_MSG') . $sms_code;
                break;
            default:
                break;
        }

        $send_result = sendMessageRequest($mobile, $sms_content);

        // 保存短信信息
        $data['sms_content'] = $sms_content;
        $data['sms_code'] = $sms_code;
        $data['mobile'] = $mobile;
        $data['type'] = $msg;
        // $data['send_status'] = $send_result['status'];
        //$data['send_response_msg'] = $send_result['info'];
        D('Common/SmsMessage')->addSmsMessage($data);
//        $send_result['status']=1;
        if ($send_result['status'] == 1) {
            $this->ajaxReturn(V(1, '发送成功'));
        } else {
            $this->ajaxReturn(V(0, '发送失败:'. $send_result['info']));
        }
    }

    /**
     * 验证 短信验证是否正确
     * @param unknown_type $code
     * @param unknown_type $mobile
     */
    public function checkPhoneVerify($code,$mobile,$type){
        $where['sms_code'] = $code;
        $where['mobile'] = $mobile;
        //$where['type'] = $type;
       // $where['add_time'] = array('ELT', NOW_TIME - C('SMS_EXPIRE_TIME') * 60);
        $where['add_time'] = array('EGT', NOW_TIME - 5 * 60);
        $smsMessage = M('SmsMessage');
        // 查询最新验证对应的主键id
        $id = $smsMessage->where($where)->find();

//        $where['id'] = $id;
        if (count($id) > 0) {
            return $id;
        } else{
            return '';
        }
    }
    /**
     * 验证 邮箱验证是否正确
     * @param unknown_type $code
     * @param unknown_type $mobile
     */
    public function checkEmailVerify($code,$email){
        $where['code'] = $code;
        $where['email'] = $email;
        $where['send_status'] = 1;
        $where['add_time'] = array('EGT', NOW_TIME - 10 * 60);

        $emailMessage = M('EmailMessage');
        // 查询最新验证对应的主键id
        $id = $emailMessage->where($where)->find();

        if (count($id) > 0) {
            return $id;
        } else{
            return '';
        }
    }
    //发送验证码
    public function sendMessage(){
        $mobile = I('mobile');
        //验证手机号
        if ($mobile == '' || is_mobile($mobile) == false) {
            $this->apiReturn(0,'手机号码不正确');
        }
        $type = I('type',1);
        //1.注册 2.修改手机号 3.找回密码
        $type_array = array(1, 2 , 3, 4, 5, 6);
        if (!in_array($type, $type_array)) {
            $this->apiReturn(0, '参数错误');
        }

        $code = rand(100000, 999999);
        $message = "您的验证码为：".$code.",请您在10分钟内使用。";
        $result = sendMessageRequest($mobile,$message);
        $smsMessage = M('SmsMessage');
        $date['mobile']=$mobile;
        $date['sms_content']=$message;
        $date['sms_code']=$code;
        $date['add_time']=time();
        $date['send_status']=$result['status'];
        $date['type']=$type;

        $smsMessage->add($date);

        if ($result['status'] == 1) {
            $this->apiReturn('1', '发送成功');
        } else {
            $this->apiReturn('0', '获取验证码失败',$result['info']);
        }



    }
    //找回密码
    public function findpwd(){
        $username = I('username','');
        $code = I('mobile_code');
        //$mobile_code = session('findpwdcode');

        $data['password'] = md5(I('password'));
//        if($code != $mobile_code){
//            $this->ajaxReturn(V(0, '验证码不正确'));
//        }
        $re_code = $this->checkPhoneVerify($code,$username);
        // p($re_code);die;
        if (!$re_code) {
            $this->apiReturn(0, '验证码错误或已过期！');
        }
        if(!$username){
            $this->apiReturn(0, '请输入用户名');
        }
        if(!$code){
            $this->apiReturn(0, '请输入验证码');
        }
        if(!I('password')){
            $this->apiReturn(0, '请输入密码');
        }
        $user = M('User');

        //如果是电话号码，执行短信平台
        if(isMobile($username)===true){
            $result = $user->where('phone='.$username)->save($data);

        }
        if(isEmail($username)===true){
            $where['email'] = $username;
            $result = $user->where($where)->save($data);

        }

        if($result !== false){
            $this->apiReturn(1, '修改密码成功');
        }else{
            $this->apiReturn(0, '修改密码失败');
        }
    }

    //发送邮件验证码
    public function get_email_code(){
        $username = I('email','');
        // 执行邮箱平台
        if(isEmail($username)===true){
            $address = $username;
            $subject = '每天收卡邮箱绑定';
            $code = rand(1000,9999);

            /*    $content = "Dear" . $aaiton code is :".$code."<br/>If you havn't sent the request of reseting password, pls neglect this mail.";*/
            $content = "亲爱的" . $address . "：<br/>您在" . time_format(time()) . "提交了邮箱绑定请求。<br/>您的验证码为：".$code."<br/>如果您没有提交邮箱绑定请求，请忽略此邮件。";
            $res = send_email($address,$subject,$content);
            $data['email_content']=$content;
            $data['add_time']=time();
            $data['email_code']=$code;
            $data['email']=$username;
            $data['send_status']=$res['status'];
            $data['status']=I('status');///1修改邮箱 2忘记密码
            $data['send_response_msg']=$res['message'];
            $r=M('EmailMessage')->add($data);
            if($res['status']==1){
                session('mobile_code',$code,'600');
                session('findpwname',$address,'600');
                $this->ajaxReturn(V(1,'邮件已发送，请在10分钟内填写验证码'));
            }else{
                $this->ajaxReturn(V(0,$res['message']));
            }

        }
    }

    /*
     * 微信第三方登录
     * photo_path
     */
    public function mobileLogin()
    {
        $openid = I('openid');
        $photo_paths = I('photo_path');
        $nickname= I('nickname');
        $userModel = D('Common/User');
        $type = I('type',1); //微信 1 qq2
        $photo_path =base64_decode($photo_paths);

        $loginInfo = $userModel->wxlogin($openid,$photo_path,$nickname,$type);
        if( $loginInfo['status'] == 1 ){ //登录成功
            $this->ajaxReturn($loginInfo);
        } else {
            $this->ajaxReturn($loginInfo);
        }
    }

}

