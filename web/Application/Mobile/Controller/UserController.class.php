<?php
namespace Mobile\Controller;

/**
 * 前台首页控制器
 */
class UserController extends HomeCommonController {
    public $user_id = 0;
 /*   public function _initialize() {
        $userauth = session('user_auth');
        if(!$userauth){
        //  $this->display('login');
            $this->redirect('Mobile/User/login');
        }
    }*/

    public function index(){
        $phone = session('phone');
        $userauth = session('user_auth');
        if(!$userauth){
        //  $this->display('login');
            $this->redirect('Mobile/User/login');
        }
        //查询用户的个人信息
        $user = M('User');
        $user_id = $userauth['id'];
        $user_info = $user->where('id='.$user_id)->find();

        //根据用户id获得用户默认的地址信息
        $where['user'] = $user_id;
        $where['flag'] = 1;
        $money = M('PaymentRecord')->where($where)->sum('pay_cash');
        $this->assign('money',$money);
        $region_list = get_region_list();
        $this->assign('region_list',$region_list);
        $default_address = get_user_default_address($user_id);
        $this->assign('default_address',$default_address);
        $this->assign('user_info',$user_info);
        $this->display();
    }

    public function repassword(){
        $userauth = session('user_auth');

        if(!$userauth){
        //  $this->display('login');
            $this->redirect('Mobile/User/login');
        }
        
         $this->display();

    }
    //用户登录
    public function login(){
        if(IS_POST){
            $is_auto_login = I('is_auto_login',0,'intval');
            $phone = I('phone');
            $password = I('password');
            $user = M('User');
            $check_phone = $user->where('phone='.$phone)->count();
            if(!($check_phone > 0)){
                $this->ajaxReturn(V(0, '该手机号未注册'));
            }
            $condition['phone'] = $phone;
            $condition['password'] = md5($password);
            $result = $user->where($condition)->count();
            if($result == 1){
                session('phone', $phone);
                $user = $user->where('phone='.$phone)->find();
                session('user_auth',$user);
                if($is_auto_login==1){
                    //自动登录
                    cookie('user_info',$userinfo,'99999999');
                }
                $this->ajaxReturn(V(1, '登录成功'));
            }else{
              $this->ajaxReturn(V(0, '密码输入不正确'));
            }
        }else{
            $this->display();
        }
    }
    
    //短信获得验证码
    public function getCode(){
        $mobile = I('mobile','');
        $code = $this->generate_code(4);
        session('code',$code);
        $message = "您的验证码为：".$code.",请您在10分钟内使用。";
        $result = sendMessageRequest($mobile,$message);
        if ($result['status'] == 1) {
            $this->ajaxReturn(V(1, '验证码已发送'));
        } else {
            $this->ajaxReturn(V(0, '获取验证码失败'));
        }
    }
    //生成随机数
    function generate_code($length = 4) {
        return rand(pow(10,($length-1)), pow(10,$length)-1);
    }
    //用户注册
    public function register(){
        if (IS_POST) {
            $user = M('User');
            $code = session('phone_code');
            $password = I('password');
            $phone = I('phone');
            $mobile_code = I('mobile_code');
            if($mobile_code != $code){
                $this->ajaxReturn(V(0, '验证码输入不正确'));
            }
            $count = $user->where('phone='.$phone)->count();
            if($count > 0){
                $this->ajaxReturn(V(0, '该手机号码已经注册'));
            }
            $data['username'] = $phone;
            $data['phone'] = $phone;
            $data['password'] = md5($password);
            $data['add_time'] = time();
            $rest = $user->add($data);
            if($rest !==false){
                $this->ajaxReturn(V(1, '注册成功'));
            }else{
                $this->ajaxReturn(V(0, '注册失败'));
            }
        }else{
            $this->display();
        }
    }
    //验证手机号码是否重复
    public function checkPhoneUnique(){
        $tel = I('tel');
        $user = M('User');
        $count = $user->where('phone='.$tel)->count();
        if($count > 0){
            $res['status']= -1;
            $res['info'] = "该手机号码已经注册！";
            $this->ajaxReturn($res);
        }else{
            $res['status']= -2;
            $res['info'] = "该手机号码未注册！";
            $this->ajaxReturn($res);
        }
    }
    //修改密码
    public function re_password(){
          $res_login = is_login();
        $user_id = $res_login['id'];
        if($user_id == 0){
            $this->redirect('Mobile/User/login');
        }
        $password = I('password');
        $phone = I('phone');
        $user = M('User');
        $update_data['password'] = md5($password);
        $count = $user->where('phone='.$phone)->count();
        if($count == 0){
            $this->ajaxReturn(V(0, '该用户未注册'));
        }
        $result = $user->where('phone='.$phone)->save($update_data);
        if($result !== false){
            $this->ajaxReturn(V(1, '修改密码成功'));
        }else{
            $this->ajaxReturn(V(0, '修改密码失败'));
        }
    }

     public function forget_password(){
        $this->display();

     }
//修改登录密码
   public function editpassword(){

        $userauth = session('user_auth');
        $user_id = $userauth['id'];

        if($user_id == 0){
            $this->redirect('Mobile/User/login');
        }
        $phone = session('phone');
        $oldpassword =md5( I('oldpa'));
        $newpassword = md5(I('newpa'));
        $user = M('User');
        $userinfo = $user->where('id='.$user_id)->find();
        if($userinfo['password'] && $userinfo['password'] != $oldpassword){
            $this->ajaxReturn(V(0, '原密码不正确'));
        }
        $update_data['password'] = $newpassword;
        $result = $user->where('id='.$user_id)->save($update_data);
        if($result !== false){
            $this->ajaxReturn(V(1, '修改密码成功'));
        }else{
            $this->ajaxReturn(V(0, '修改密码失败'));
        }
    }

    //修改支付密码
    public function change_pay_password(){
        if(IS_POST){
            $pay_password = I('pay_password');
            $phone = I('phone');
            $user = M('User');
            $count = $user->where('phone='.$phone)->count();
            if($count == 0){
                $this->ajaxReturn(V(0, '该用户未注册'));
            }
            $update_data['pay_password'] = md5($pay_password);
            $result = $user->where('phone='.$phone)->save($update_data);
            if($result !== false){
                $this->ajaxReturn(V(1, '修改支付密码成功'));
            }else{
                $this->ajaxReturn(V(0, '修改支付密码失败'));
            }
        }else{
            $userauth = session('user_auth');
            $user_id = $userauth['id'];

            if($user_id == 0){
                $this->redirect('Mobile/User/login');
            }
            $user_info = M('User')->where('id='.$user_id)->find();
            $this->assign('user_info',$user_info);
            $this->display('change_pay_password');
        }

    }
    //实名认证
    public function real_name(){
        $phone = session('phone');
        $userauth = session('user_auth');
        $user_id = $userauth['id'];

        if($user_id == 0){
            $this->redirect('Mobile/User/login');
        }
        $user = M('User');
        if(IS_POST){
            $append['name'] = I('real_name');
            $append['id_card'] = I('Id_card');
            $append['id_card_phone_upon']=I('id_card_phone_upon');
            $append['id_card_phone_down']=I('id_card_phone_down');
            $append['id_card_phone_hold']=I('id_card_phone_hold');
            /* if($_FILES[positive][tmp_name][0] || $_FILES[negative][tmp_name][0] ){
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize   =     (1024*1024*3);// 设置附件上传大小 管理员10M  否则 3M
                $upload->exts      =    array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->rootPath  =    './Uploads/'; // 设置附件上传根目录
                $upload->replace   =    true; // 存在同名文件是否是覆盖，默认为false
                $upload_info  =  $upload->upload();// 上传文件
                if(!$upload_info){// 上传错误提示错误信息
                    $this->error($upload->getError());
                }else{


                    foreach($upload_info as $key => $val)
                    {
                        if($val['key'] == 'positive'){
                            $append['id_card_phone_upon'] = '/Uploads/'.$val['savepath'].$val['savename'];
                        }else{
                            $append['id_card_phone_down'] = '/Uploads/'.$val['savepath'].$val['savename'];
                        }
                    }
                }
            }*/
            $append['is_permission'] = 2;
            $result = $user->where('id='.$user_id)->save($append);
            if($result !== false){
                echo '<script>alert("提交资料成功");</script>';
                $this->redirect('Mobile/User/index');
                die;
            }else{
                $this->ajaxReturn(V(0, '提交资料失败！'));
                //echo '<script>alert("提交资料失败");</script>';
                //$this->display('index');
            }
        }else{
            //获得用户提交的资料信息
            $user_info = $user->where('id='.$user_id)->field('id,name,id_card,id_card_phone_upon,id_card_phone_hold,id_card_phone_down,is_permission')->find();
            $this->assign('user_info',$user_info);
            $this->display();
        }

    }


    // base64上传头像
    public function editAvatar(){
        $img = I('avatar');         //获取图片
        $return = $this->img_upload($img);
        if ($return['code'] == 1) {
            //压缩图片地址
            $data['avatar'] = thumb('/Uploads/'.$return['url'],350,220);
             $data['realavatar'] = '/Uploads/'.$return['url'];
           // M('User')->where(array('id'=>UID))->save($data);
            //$this->returnApiSuccess($return['msg'], $data['avatar']);

            $this->ajaxReturn(V(1, $data['realavatar'], $data['avatar']));
        } else {
            //$this->returnApiError($return['msg']);
            $this->ajaxReturn(V(0, $return['msg']));
        }
    }

    /**
     * base64图片上传
     * @param $base64_img
     * @return array
     */
    private static function img_upload($base64_img){
        $base64_img = trim($base64_img);
        $time = time();
        $date = date('Y-m-d');
        $up_dir = './Uploads/Picture/Avatar/' . $date . '/';
        if (!file_exists($up_dir)) {
            mkdir($up_dir, 0777, true);
        }
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_img, $result)) {
            $type = $result[2];
            if (in_array($type, array('pjpeg', 'jpeg', 'jpg', 'gif', 'bmp', 'png'))) {
                $new_file = $up_dir . $time . '.' . $type;
                if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_img)))) {
                    $img_path = str_replace('./Uploads/', '', $new_file);
                    return array('code' => 1, 'msg' => "图片上传成功", 'url' => $img_path);
                }
                return array('code' => 2, 'msg' => "图片上传失败");
            }
            //文件类型错误
            return array('code' => 4, 'msg' => "文件类型错误");
        }
        //文件错误
        return array('code' => 3, 'msg' => "文件错误");
    }

    //qq第三方登录
    public function qq_login(){
       /* $access_token = I('access_token','');
        $graph_url = "https://graph.qq.com/oauth2.0/me?access_token=".$access_token;
        $str  = file_get_contents($graph_url);
        if (strpos($str, "callback") !== false){
            $lpos = strpos($str, "(");
            $rpos = strrpos($str, ")");
            $str  = substr($str, $lpos + 1, $rpos - $lpos -1);
            $msg = json_decode($str,true);
        }
        $OpenID = $msg['openid'];
        $url2 = "https://graph.qq.com/user/get_user_info?access_token=".$access_token.'&oauth_consumer_key=101383545'.'&openid='.$OpenID;
        $res = file_get_contents($url2);//获取个人信息
        $memberInfo = json_decode($res,true);
        $user = array(
            'qq_openid'=>$OpenID,
            'qq_usernick'=>$memberInfo['nickname'],
            'qq_avatar_thumb'=>$memberInfo['figureurl_qq_1'],
        );
        $where['qq_openid'] = $OpenID;
        $info = M('User')->where($where)->find();
        if($info==''){
            $res = M('User')->add($user);
            $userInfo = M('User')->where('id='.$res)->find();
        }else{
            $userInfo = $info;
        }
        session('user_info',null);
        session('user_info',$userInfo);*/
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
        $user = array(
                'qq_openid'=>$OpenID,
                'qq_usernick'=>$memberInfo['nickname'],
                'qq_avatar_thumb'=>$memberInfo['figureurl_qq_1'],
                'username'=>$memberInfo['nickname'],

            );

        $where['qq_openid'] = $OpenID;
        $users = M('User');
        $info = $users->where($where)->find();


        if($info==''){
             $userauth = session('user_auth');
              if(!$userauth){
                $user['add_time'] = time();
                  LL($user);
                  $res = $users->add($user);
                  LL($users->_sql());
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

        $this->redirect('Mobile/Index/index');
    }

    /*
    * 用户地址列表
    */
    public function address_list(){
        $phone = session('phone');
        $user_id = get_user_id($phone);
        $address_lists = get_user_address_list($user_id);
        $region_list = get_region_list();
        $this->assign('region_list',$region_list);
        $this->assign('lists',$address_lists);
        $this->display();
    }

    /*
    * 添加地址
    */
    public function add_address() {
        $p = M('region')->where(array('parent_id' => 0, 'level' => 1))->select();
        $this->assign('province', $p);
        $this->display('edit_address');
    }

    /*
    * 提现
    */
    public function withdrawals() {
         $userauth = session('user_auth');
        $user_id = $userauth['id'];

        if($user_id == 0){
            $this->redirect('Mobile/User/login');
        }
        //查询用户的个人信息
        $user = M('User');
        $map['falg_type'] = 1;
        $map['user'] = $user_id;
         $paymentrecord = M('PaymentRecord')->where($map)->order('add_time desc')->limit(1)->select();
        $default_alipay = $paymentrecord[0]['alipaycount'];

        $user_info = $user->where('id='.$user_id)->find();
        $this->assign('user_info',$user_info);
        $this->assign('default_alipay',$default_alipay);
        $this->display('withdrawals');
    }
      //修改提现银行卡
    public function editbankcard(){
        $userauth = session('user_auth');
        $user_id = $userauth['id'];
        $user = M('User');
        $userinfo  =M('User')->where('id='.$user_id)->find();
        if($userinfo['name'] == ''||$userinfo['is_permission'] != 1){
            $this->ajaxReturn(V(0,'请先实名认证')); 
        }
        if($userinfo['name'] != I('username')){
            $this->ajaxReturn(V(0,'只能提现到本人银行卡')); 
        }
        $update_data['bankname'] = I('bankname');
        $update_data['bankcardnum'] = I('bankcardnum');
        $update_data['bankphone'] = I('bankphone');
        $result = $user->where('id='.$user_id)->save($update_data);
        if($result !== false){
            $this->ajaxReturn(V(1, '修改密码成功'));
        }else{
            $this->ajaxReturn(V(0, '修改密码失败'));
        }
    }
    //提现申请
    function withcash(){
        $userauth = session('user_auth');
        $user_id = $userauth['id'];

        $password = md5(I('paypassword'));
         $userinfo  =M('User')->where('id='.$user_id)->find();
          $map['user'] = $user_id;
        $map['flag'] = 1;
        $waitcashmoney = M('PaymentRecord')->where($map)->sum('pay_cash');
        $waitcashmoney = $waitcashmoney + I('money');
        if($userinfo['balance'] < $waitcashmoney) {
             $this->ajaxReturn(V(0,'余额不足'));
        } 
        $oldpassword =$userinfo['pay_password'];
        if($oldpassword == ''){
            $data['pay_password'] = $password;
            M('User')->where('id='.$user_id)->save($data);
            unset($data);

        }else if($oldpassword != $password){
            $this->ajaxReturn(V(0,'密码不正确'));

        }
        if($userinfo['name'] == ''||$userinfo['is_permission'] != 1){
            $this->ajaxReturn(V(0,'请先实名认证')); 
        }
        $data['member_name'] = $userinfo['name'];
        $data['alipaycount'] = I('alipay');
        $data['pay_cash'] = I('money');
        $data['falg_type'] = I('paytype');
        //默认支付宝提现
        if($data['falg_type'] != 2){
            $data['falg_type'] = 1;
        }
        if($data['pay_cash'] < 1) {
             $this->ajaxReturn(V(0,'提现金额输入错误'));
        }
        $data['add_time'] = time();
        $data['balance']=$userinfo['balance'];
        $data['user'] = $user_id;
        $data['sale_record_id'] ='2017'.date('mdHis').rand(1000,9999);

        $result =  M('PaymentRecord')->add($data);
        if($result){$this->ajaxReturn(V(1,'申请成功'));}

    }
    //修改支付密码
       public function editzfpassword(){
        $userauth = session('user_auth');
        $user_id = $userauth['id'];
        $paypassword =md5( I('password'));
        $code =  I('code');
        $sendcode =  session('zfphone_code');
        $phone = session('zfphone_number');
        if($code != $sendcode){
                $this->ajaxReturn(V(0, '验证码不正确'));
            }

        $user = M('User');
        $userinfo = $user->where('id='.$user_id)->find();

        if($userinfo['phone'] == ''){
          $update_data['phone']  =  $phone;
        }else{
             if($userinfo['phone'] != $phone){
                $this->ajaxReturn(V(0, '手机号码不正确'));
            }
        }

        $update_data['pay_password'] = $paypassword;
        $result = $user->where('id='.$user_id)->save($update_data);
        if($result !== false){
            $this->ajaxReturn(V(1, '修改支付密码成功'));
        }else{
            $this->ajaxReturn(V(0, '修改支付密码失败'));
        }
    }


    /*
   * 提现历史
   */
    public function withdrawalshistory() {
        $userauth = session('user_auth');
        $where['user']=$userauth['id'];
        $paymentinfo = M('PaymentRecord')->where($where)->order('id desc')->select();
        $this->assign('payinfo',$paymentinfo);


        $this->display('withdrawalshistory');
    }

    //保存添加地址
    public function save_address(){
        $phone = session('phone');
        $user_id = get_user_id($phone);
        $id = I('id',0 ,intval);
        $data = add_address($user_id, $id, I('post.'));
        if ($data['status'] != 1){
            $this->ajaxReturn(V(0, $data['msg']));
        }else{
            $this->ajaxReturn(V(1, $data['msg']));
        }
    }
    /*
     * 地址编辑
     */
    public function edit_address() {
        $phone = session('phone');
        $user_id = get_user_id($phone);
        $id = I('id');
        $address = M('user_address')->where(array('address_id' => $id, 'user_id' => $user_id ))->find();
        //获取省份
        $p = M('region')->where(array('parent_id' => 0, 'level' => 1))->select();
        $c = M('region')->where(array('parent_id' => $address['province'], 'level' => 2))->select();
        $d = M('region')->where(array('parent_id' => $address['city'], 'level' => 3))->select();
        if ($address['twon']) {
            $e = M('region')->where(array('parent_id' => $address['district'], 'level' => 4))->select();
            $this->assign('twon', $e);
        }
        $this->assign('province', $p);
        $this->assign('city', $c);
        $this->assign('district', $d);
        $this->assign('address', $address);
        $this->display();
    }
    //修改邮箱绑定
    public function email() {
         $this->display();

    }
 //修改手机绑定
     public function phone() {
         $this->display();

    }

    /*
     * 设置默认收货地址
     */
    public function set_default() {
        $id = I('id');
        $phone = session('phone');
        if(!$phone){
            $this->display('login');
        }
        $user_id = get_user_id($phone);
        M('user_address')->where(array('user_id' => $user_id))->save(array('is_default' => 0));
        $row = M('user_address')->where(array('user_id' =>$user_id, 'address_id' => $id))->save(array('is_default' => 1));
        if ($row !== false){
            $this->ajaxReturn(V(1, '设置成功！'));
        }else{
            $this->ajaxReturn(V(0, '设置失败！'));
        }
    }

    /*
     * 地址删除
     */
    public function del_address() {
        $id = I('id');
        $phone = session('phone');
        $user_id = get_user_id($phone);
        $address = M('user_address')->where("address_id = $id")->find();
        $row = M('user_address')->where(array('user_id' => $user_id, 'address_id' => $id))->delete();
        // 如果删除的是默认收货地址 则要把第一个地址设置为默认收货地址
        if ($address['is_default'] == 1) {
            $address2 = M('user_address')->where("user_id = ".$user_id)->find();
            $address2 && M('user_address')->where("address_id = {$address2['address_id']}")->save(array('is_default' => 1));
        }
        if ($row !== false){
            $this->ajaxReturn(V(1, '删除成功！'));
        }else{
            $this->ajaxReturn(V(0, '删除失败！'));
        }
    }
    
    function login_out(){
        session(null);
        session('user_auth',null);
        cookie("phone", null);
        cookie("user_info", null);
        setcookie ("user_info", "", time() - 3600);
        setcookie ("phone", "", time() - 3600);
        $this->display('Index/index');
    }



}