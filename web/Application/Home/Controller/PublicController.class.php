<?php
namespace Home\Controller;
Use Think\Log;
use Think\Verify;

/**
 *  前台首页控制器
 * @author guolt
 *
 */
class PublicController extends HomeCommonController {



	/**
	 * 设置语言
	 */
	public function lang(){
		$lang = I('lang','','trim,strtolower');
		$langArr = explode(',',	C('LANG_LIST'));

		if(in_array($lang,$langArr)){

			cookie('think_language',$lang,31536000);

		}else{
			cookie('think_language',null);
		}
		redirect('/Home/Index/index', 0, '页面跳转中...');
	}



	/* 注册页面 */
	public function register(){



		$this->display();

	}

	/* 用户弹框登录 */
	public function loginBox(){
		 



		$this->display("login");
	}
	
	//上传身份证信息
	public function uploadImg(){
		$this->_uploadImg();
	}
	//上传图片方法
	function _uploadImg(){
		$oldImg = I('oldImg', '', 'htmlspecialchars');
		$savePath = I('savePath', '', 'htmlspecialchars');

		if($savePath != '') $savePath = $savePath . '/';

		$result = array( 'status' => 1, 'msg' => '上传完成');
		//判断有没有上传图片
		//p(trim($_FILES['photo2']['name']));
		if(trim($_FILES['photo']['name']) != ''){
			$upload = new \Think\Upload(C('PICTURE_UPLOAD')); // 实例化上传类
			$upload->replace  = true; //覆盖
			$upload->savePath = $savePath; //定义上传目录
			//如果有上传名, 用原来的名字
			if($oldImg != '') $upload->saveName = $oldImg;
			// 上传文件
			$info = $upload->uploadOne($_FILES['photo']);
			if(!$info) {
				$result = array( 'status' => 0, 'msg' => $upload->getError() );
			}else{
				if ($oldImg != '') {
					//删除缩略图
					$dir = '.'.C('UPLOAD_PICTURE_ROOT') . '/' . $info['savepath'];
					$filesnames = scandir($dir);
					foreach ($filesnames as $key => $value) {
						if ($value === '.' || $value === '..') {
							continue;
						}
						$count = strpos($value, $oldImg.'_');
						if ($count !== false) {
							$file = '.' . __ROOT__ . C('UPLOAD_PICTURE_ROOT') . '/' . $info['savepath'].$value;
							if (file_exists($file) == true) {
								@unlink($file);
							}
						}
					}
				}
				$result['src'] = C('UPLOAD_PICTURE_ROOT') . '/' . $info['savepath'] . $info['savename'];
			}
			$this->ajaxReturn($result);
		}
	}
	//实名验证
	public function real_name(){
		 $userauth = session('user_auth');

        if(!$userauth){
            $this->redirect('Home/Idex/index');
        
        }
        //查询用户的个人信息
        $user = M('User');
        $user_id = $userauth['id'];
		if(IS_POST){
			$append['name'] = I('real_name');
			$append['id_card'] = I('Id_card');
			$append['id_card_phone_upon'] = I('id_card_phone_upon');
			$append['id_card_phone_down'] = I('id_card_phone_down');
            $append['id_card_phone_hold'] = I('id_card_phone_hold');
			$append['is_permission'] = 2;
			$result = $user->where('id='.$user_id)->save($append);
			if($result !== false){
				$this->ajaxReturn(V(1, '提交资料成功！'));
			}else{
				$this->ajaxReturn(V(0, '提交资料失败！'));
			}
		}else{
			//获得用户提交的资料信息
			$user_info = $user->where('id='.$user_id)->field('id,name,id_card,id_card_phone_upon,id_card_phone_down,is_permission,id_card_phone_hold')->find();
			$this->assign('user_info',$user_info);
			$this->display();
		}
	}


	/* 登录页面 */
	public function login($username = '', $password = '', $verify = ''){
		if(IS_POST){
             $is_auto_login = I('is_auto_login',0,'intval');
			/* 检测验证码  */
			if(!check_verify($verify)){
				 $this->error('验证码输入错误！');
			}
			/* 调用UC登录接口登录 */
			$User = D('Common/Member');

			$reInfo = $User->login($username, $password,3);
			if( $reInfo['status']>0 ){ //登录成功
				/* 存入session */
				autoSession($reInfo['data']);
				//$this->autoSession($reInfo['data']);
				//TODO:跳转到登录前页面

				$this->success('登录成功！', U('Index/index'));
			} else { //登录失败
				$error =  $reInfo['msg'];
				$this->error($error);
			}
		} else {
			if(is_login()){
				$this->redirect('Index/index');
			}else{
				$this->display();
			}
		}
	}

	// 登录注册
	public function login_new(){
		$this->display();
	}

	public function verify() {
		//$verify = new \Think\Verify();
		//$verify->entry(1);
        ob_clean();//丢弃输出缓冲区中的内容
		$config = array(
            'useImgBg'  => true,           // 使用背景图片
            'useNoise'  => false,            // 是否添加杂点
			'fontttf' => '4.ttf',         // 验证码字体，不设置随机获取
			'length' => 4,               // 验证码位数
			'useCurve' => false,           // 是否画混淆曲线
		);
		$verify = new Verify($config);
		$verify->entry(1);//输出验证码

	}


    /* AJAX登录*/
    public function ajaxLogin($login_name = '', $password = ''){
    	if(IS_AJAX){
    		/* 调用UC登录接口登录 */
    		$User = D('Common/Member');
    		$reInfo = $User->login($login_name, $password,3);


    		if( $reInfo['status']>0 ){ //登录成功

    			/* 存入session */
				autoSession($reInfo['data']);
    			//TODO:跳转到登录前页面
    			 $this->ajaxReturn($reInfo);
    		} else { //登录失败
    			$this->ajaxReturn($reInfo);
    		}
    	} else {
    		$this->error('非法操作');
    	}
    }

    /* 注册页面 */
    public function ajaxRegister(){
		$User = D('Common/Member');

		$phone=I('phone','');
		$password = I('password','','htmlspecialchars');
		$email=I('email','','htmlspecialchars');

        $reInfo = $User->register($phone,$password,$email);

		if( $reInfo['status']>0 ){ //注册成功
			//加入预约表
			$uid = $reInfo['data'];
			/* 存入session */
            $user = M('Member')->where('id='.$uid)->find();
            session('user_auth',$user);
			// autoSession($user);
			//跳转到登录前页面
			$this->ajaxReturn($reInfo);
		} else { //登录失败
			$this->ajaxReturn($reInfo);
		}

    }

    /**
     * 退出
     */
    public function logout(){
    	//session(null);
        session('phone',null);
        session('user_auth',null);
        cookie("phone", null);
        cookie("user_info", null);
        setcookie ("user_info", "", time() - 3600);
        setcookie ("phone", "", time() - 3600);
   
    	$this->redirect('Index/index');
    }
	/**
	 * 找回密码
	 */


	public function findPassword(){
		if(IS_POST){
			$login_name = I('login_name');
			$verifycode = I('verifycode');
			$way = I('way');
			/* 检测验证码  */
			if(!check_verify($verifycode)){
				$this->ajaxReturn(return_status( 0, L('code_not_correct')));
			}
			$member = M('Member')->where(array('login_name'=>trim($login_name),'status'=>1))->find();
			if($member){
				if($way==0){//邮箱
					$mailsubject = L('Forgot_password_application');
					$PHP_SELF=$_SERVER['PHP_SELF'];
					$url='http://'.$_SERVER['HTTP_HOST'].substr($PHP_SELF,0,strrpos($PHP_SELF,'/')+1);
					$url = $url."findpasswordtwo?pwtokenkey=".$this->putTokenKey($login_name);
					$mailbody = L('Please_click_here').'<br> ';
					$mailbody .= '<a style="color:red" href="'.$url.'">'.$url.'</a>';
					$emailInfo = sendEmail($login_name, $mailsubject, $mailbody, 'HTML');
					if ($emailInfo) {
						$this->ajaxReturn(return_status( 1, L('Retrieve_password_link')));
					}else{
						$this->ajaxReturn(return_status( 0, L('Mailbox_send_failed')));
					}
				}
			}else{
				$this->ajaxReturn(return_status( 0, L('user_disable')));
			}
		}else{
			$this->display();
		}
	}
	//获取支付验证码的函数
    public function getzfCode(){
        $username = I('username','');
        //获取随机的四位数字
        $code = rand(1000,9999);
        $user = M('User');
        $userauth = session('user_auth');
        $user_id = $userauth['id'];
        $phone = $user->where('id='.$user_id)->getField('phone');

        if($phone && $username != $phone){
            $this->ajaxReturn(V(0, '手机号码不正确'));
        }

        //如果是电话号码，执行短信平台
        if(isMobile($username)===true){
            $mobile = $username;
            $content = "您正在修改支付密码,验证码是".$code."，请于30分钟内输入，工作人员不会向您索取，请勿泄露。";
            $res = sendMessageRequest($mobile,$content);
            if($res['status']==1){
                session('zfphone_code',$code,'600');
                session('zfphone_number',$mobile,'600');
                $this->ajaxReturn(V(1,'短信发送成功，请在10分钟内填写有效的验证码'));
            }else{
                $this->ajaxReturn(V(0,$res['info']));
            }
        }
}

	//获取验证码的函数
    public function getCode(){
        $username = I('username','');
        //获取随机的四位数字
        $code = rand(1000,9999);
        $user = M('User');

        $count = $user->where('phone='.$username)->count();
        if($count > 0){
            $this->ajaxReturn(V(0, '该手机号码已经注册'));
        }

        //如果是电话号码，执行短信平台
        if(isMobile($username)===true){
            $mobile = $username;
            $content = "您正在绑定手机,验证码是".$code."，请于30分钟内输入，工作人员不会向您索取，请勿泄露。";
            $res = sendMessageRequest($mobile,$content);
            if($res['status']==1){
                session('phone_code',$code,'600');
                session('phone_number',$mobile,'600');
                $smsjudge = M('SmsMessage');

                $date['mobile']=$mobile;
                $date['sms_code']=$code;
                $date['add_time']=time();
                $date['sms_content']=$content;
                //$date['type']=$type;
                $smsjudge->add($date);
                $this->ajaxReturn(V(1,'短信发送成功，请在10分钟内填写有效的验证码'));
            }else{
                $this->ajaxReturn(V(0,$res['info']));
            }
        }
        // 执行邮箱平台
        if(isEmail($username)===true){
            $address = $username;
            $subject = '每天收卡网回收邮箱绑定';
            $code = rand(1000,9999);
            /*    $content = "Dear" . $aaiton code is :".$code."<br/>If you havn't sent the request of reseting password, pls neglect this mail.";*/
            $content = "亲爱的" . $address . "：<br/>您在" . time_format(time()) . "提交了邮箱绑定请求。<br/>您的验证码为：".$code."<br/>如果您没有提交邮箱绑定请求，请忽略此邮件。";
            $res = $this->send_email($address,$subject,$content);
            //$res['status']=1;
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

//验证码校验
    public function checkCode(){
    	$code = I('verification',0,'intval');
    	if($code==session('mobile_code')){
    		cookie('code',$code,100);
    		session('check_code',$code,'600');
            $this->ajaxReturn(V(1,'验证码输入正确'));

        }else{
            $this->ajaxReturn(V(0,'验证码不正确'));
        }
    }





//忘记密码--然后获取到验证码以后，进行密码修改
public function modifyPassword(){
    $username = session('findpwname');
    $password = I('password','');
    $code = I('verification',0,'intval');

    $userId = M('Member')->where(array('email'=>$username))->getField('id');
    if($userId==''){
        $this->ajaxReturn(V(0,'该邮箱未注册'));
    }

    if(strlen($password)<6){
        $this->ajaxReturn(V(0,'请输入6位及以上的新密码'));
    }

    if($code==session('mobile_code')){
        $data['password'] = md5($password);
        $res = M('Member')->where('id='.$userId)->data($data)->save();
        if($res>0){
        	$this->ajaxReturn(V(1,'Rest Successful !'));
        }else{
            $this->ajaxReturn(V(0,'Rest Failed !'));
        }
    }
}

// 邮箱发送验证码模块start
    /**
     * 发送邮件核心模块
     * @param  string $address 需要发送的邮箱地址 发送给多个地址需要写成数组形式
     * @param  string $subject 标题
     * @param  string $content 内容
     * @return boolean       是否成功
     */
    public function send_email($address,$subject,$content){
        $email_smtp=C('EMAIL_SMTP');
        $email_username=C('EMAIL_USERNAME');
        $email_password=C('EMAIL_PASSWORD');
        $email_from_name=C('EMAIL_FROM_NAME');
        if(empty($email_smtp) || empty($email_username) || empty($email_password) || empty($email_from_name)){
            return array("status"=>0,"message"=>'邮箱配置不完整');
        }
        require './ThinkPHP/Library/Org/Nx/class.phpmailer.php';
        require './ThinkPHP/Library/Org/Nx/class.smtp.php';
        $phpmailer=new \Phpmailer();
        // 设置PHPMailer使用SMTP服务器发送Email
        $phpmailer->IsSMTP();
        // 设置为html格式
        $phpmailer->IsHTML(true);
        // 设置邮件的字符编码'
        $phpmailer->CharSet='UTF-8';
        // 设置SMTP服务器。
        $phpmailer->Host=$email_smtp;
        // 设置为"需要验证"
        $phpmailer->SMTPAuth=true;
        // 设置用户名
        $phpmailer->Username=$email_username;
        // 设置密码
        $phpmailer->Password=$email_password;
        // 设置邮件头的From字段。
        $phpmailer->From=$email_username;
        // 设置发件人名字
        $phpmailer->FromName=$email_from_name;
        // 添加收件人地址，可以多次使用来添加多个收件人
          $phpmailer->SMTPSecure = 'ssl';
        $phpmailer->Port = '465';
      //  $phpmailer->SMTPDebug = 2; //用于debug PHPMailer信息
        if(is_array($address)){
            foreach($address as $addressv){
                $phpmailer->AddAddress($addressv);
            }
        }else{
            $phpmailer->AddAddress($address);
        }
        // 设置邮件标题
        $phpmailer->Subject=$subject;
        // 设置邮件正文
        $phpmailer->Body=$content;
        // 发送邮件。
        if(!$phpmailer->Send()) {
            $phpmailererror=$phpmailer->ErrorInfo;
            return array("status"=>0,"message"=>$phpmailererror);
        }else{
            return array("status"=>1,'message'=>'邮件已发送，请在10分钟内填写验证码');
        }
    }
//邮箱发送验证码end


	/**生成tokenkey
	 * @param $login_name
	 * @return string
	 */
	private function  _createTokenKey($login_name){
		$key = $login_name.rand(10000,99999);
		return md5($key);
	}
	/**把找回密码和邮件发送时间key存到换成中
	 * @param $login_name
	 */
	private function putTokenKey($login_name){
		$tokenKey = $this->_createTokenKey($login_name);
		S($tokenKey,$login_name,60*60);//换成1个小时
		return $tokenKey;
	}

	/**检测换成中是否已过期
	 * @param $key 加密后的login_name
	 * @return mixed
	 */
	private function getTokenKey($key){
		return S($key);
	}

	/**检测换成中是否已过期
	 * @param $key 加密后的login_name
	 * @return mixed
	 */
	private function removeTokenKey($key){
		  S($key,null);
	}

	public function findpasswordtwo(){

		$tokenKey =I('pwtokenkey');
		if($tokenKey){
			$this->assign('tokenKey',$tokenKey);
			$this->display('Public/findpassword');
		}else{
			$this->redirect('Public/findpassword');
		}

	}



    //获取找回密码的验证码函数
    public function getfindpwdCode(){
        $username = I('username','');
        //获取随机的四位数字
        $code = rand(1000,9999);
        $user = M('User');


        //如果是电话号码，执行短信平台
        if(isMobile($username)===true){
            $count = $user->where('phone='.$username)->count();
            if($count == 0){
                $this->ajaxReturn(V(0, '该手机号码还未注册'));
            }

            $mobile = $username;
            $content = "您本次的验证码是".$code;
            $res = sendMessageRequest($mobile,$content);
            if($res['status']==1){
                session('findpwdcode',$code,'600');
                $smsjudge = M('SmsMessage');
                $date['mobile']=$mobile;
                $date['sms_code']=$code;
                $date['add_time']=time();
                $date['sms_content']=$content;
                //$date['type']=$type;
                $smsjudge->add($date);
                $this->ajaxReturn(V(1,'短信发送成功，请在10分钟内填写有效的验证码'));
            }else{
                $this->ajaxReturn(V(0,$res['info']));
            }
        }


        // 执行邮箱平台
        if(isEmail($username)===true){
            $where['email'] = $username;
            $count = $user->where($where)->count();
            if($count == 0){
                $this->ajaxReturn(V(0, '该邮箱还未注册'));
            }


            $address = $username;
            $subject = '每天收卡网回收密码找回';
            $code = rand(1000,9999);

            /*    $content = "Dear" . $aaiton code is :".$code."<br/>If you havn't sent the request of reseting password, pls neglect this mail.";*/
            $content = "亲爱的" . $address . "：<br/>您在" . time_format(time()) . "提交了密码找回请求。<br/>您的验证码为：".$code."<br/>如果您没有提交密码找回请求，请忽略此邮件。";
            $res = $this->send_email($address,$subject,$content);
            if($res['status']==1){
                session('findpwdcode',$code,'600');
                session('findpwname',$address,'600');
                $this->ajaxReturn(V(1,'邮件已发送，请在10分钟内填写验证码'));
            }else{
                $this->ajaxReturn(V(0,$res['message']));
            }

        }


    }

    //用户注册
    public function register_ajax(){
        if (IS_POST) {
            $user = M('User');
            $code = session('phone_code');
            $password = I('password');
            $phone = I('phone');
            $mobile_code = I('mobile_code');
           $re_code = A('Api/PublicApi')->checkPhoneVerify($mobile_code,$phone);
            //p($re_code);die;
           // if($mobile_code != '1234'){
           if(!$re_code){
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
                $user = $user->where('phone='.$phone)->find();
                session('user_auth',$user);
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
//找回密码ddf
    public function findpwd(){

        $username = I('username','');
        $code = I('mobile_code');
       // $mobile_code = session('findpwdcode');

        $data['password'] = md5(I('password'));
        $re_code = A('Api/PublicApi')->checkPhoneVerify($code,$username);
         //p($re_code);die;
        if($re_code == '') {
            $this->ajaxReturn(V(0, '验证码错误或已过期！'));
        }

//        if($code != '1234'){
//            $this->ajaxReturn(V(0, '验证码不正确'));
//        }

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
            $this->ajaxReturn(V(1, '修改密码成功'));
        }else{
            $this->ajaxReturn(V(0, '修改密码失败'));
        }
    }
    //用户登录
    public function login_ajax(){
        if(IS_POST){
            $is_auto_login = I('is_auto_login',0,'intval');
            $phone = I('phone');
            $password = I('password');
            $user = M('User');

            $where_login['phone|email|login_account']=$phone;
            $check_phone = $user->where($where_login)->count();

            if(!($check_phone > 0)){
                $this->ajaxReturn(V(0, '该账号未注册'));
            }
            $where_login_no['disabled']=0;
            $where_login_no['phone|email|login_account']=$phone;
            $check_phone_no = $user->where($where_login_no)->count();
            if(!($check_phone_no > 0)){
                $this->ajaxReturn(V(0, '该账号已被禁用'));
            }

            $condition['phone|email|login_account'] = $phone;
            $condition['password'] = md5($password);
            $result = $user->where($condition)->count();
            if($result == 1){
                session('phone', $phone);
                $where_us['phone|email|login_account']=$phone;
                $userinfo = $user->where($where_us)->find();
                session('user_auth',$userinfo);
                if($is_auto_login==1){
                    //自动登录
                    cookie('user_info',$userinfo,'99999999');
                }
                $actionLog=M('ActionLog');
                $log['user_id']=$userinfo['id'];
                $log['add_time']=time();
                $log['type']=1;
                $log['ip']=get_client_ip();
                $actionLog->add($log);
                $this->ajaxReturn(V(1, '登录成功'));
            }else{
                $this->ajaxReturn(V(0, '密码输入不正确'));
            }
        }else{
            $this->display();
        }
    }


}
