<?php

namespace Api\Controller;

/**
 * Copyright (c) 山东六牛网络科技有限公司 https://liuniukeji.com
 *
 * @Description    个人中心
 * @Author         zhanglili  qq:1640054073
 * @Copyright      Copyright (c) 山东六牛网络科技有限公司 保留所有版权(https://www.liuniukeji.com)
 * @Date           2019.4.23
 * @CreateBy       PhpStorm
 */
class UserApiController extends CommonUserApiController
{
    //获取用户的个人信息
    public function getUserInfo(){
        //查询用户的个人信息
        $user = M('User');
        $user_id =UID;
        //$user_info = $user->where('id='.$user_id)->find();
        $user_info = $user->where('id='.$user_id)->field('login_account,qq_openid,wx_unionid,name,email,password,phone,is_permission,is_modify_username,balance,qq_avatar_thumb,weixin,login_account,qq')->find();
        $data['phone'] = $user_info['phone'];
        $data['qq_openid'] = $user_info['qq_openid'];
        $data['email'] = $user_info['email'];
        $data['wx_unionid'] = $user_info['wx_unionid'];
        $data['password'] = $user_info['password'];
        $NumberNoEmpty = 0;
        foreach($data as $vo){
            if(!empty($vo)){
                $NumberNoEmpty++;
            }
        }
        // 资料完善度
        $perfectDegree = floor(($NumberNoEmpty/5)*100);
        //$this->assign('perfectDegree', $perfectDegree);
        $user_info['perfectDegree']=$perfectDegree;
        if($user_info['email']){
           $arr = explode('@', $user_info['email']);

           $rest = substr($arr[0], -2);
           $rest2=substr($arr[0], 0, 2);
           // $arr[0] = str_replace($rest, str_repeat('*', strlen($rest)), $arr[0]);
           $user_info['email_end']=$arr[1];
           $user_info['email_name']=$rest2."***".$rest;


           //$user_info['email']=$arr[0].'@'.$arr[1];
       }
        //获取提现中的金额
        $where['user'] = $user_id;
        $where['flag'] = 1;
        $money = M('PaymentRecord')->where($where)->sum('pay_cash');
        if($money==''){$money=0;}
        $user_info['withdrawal']=$money;
        //查询提现账号
        $accountModel=M('AccountNumber');
        $account_number=$accountModel->where(array('user_id'=>$user_id))->field('id,type,cardnum')->order('id desc')->find();
        $user_info['type']=$account_number['type'];
        $user_info['cardnum']=$account_number['cardnum'];
        $this->apiReturn(1, '用户信息',$user_info);
    }


    //添加提现账号
    public function addAccountNumber(){
        $user_id = UID;
        $user = M('User');
        $userinfo  =M('User')->where('id='.$user_id)->find();

        if($userinfo['name'] == ''||$userinfo['is_permission'] != 1){
            $this->ajaxReturn(V(0,'请先实名认证'));
        }
        $data['user_id'] = $user_id;
        $data['add_time'] = time();
        $data['name'] = I('name');
        $data['cardnum'] = I('cardnum');
        $data['type']=I('type');//1支付宝 2银行卡
        if(trim($data['name']=='')){
            $this->ajaxReturn(V(0,'请填写真实姓名'));
        }
        if($data['cardnum']==''){
            $this->ajaxReturn(V(0,'信息填写不完整'));
        }
        if($userinfo['name'] != I('name')){
            $this->ajaxReturn(V(0,'只能提现到本人账号'));
        }
        $accountModel=M('AccountNumber');
        if($data['type']==2) {
            $data['phone'] = I('phone');
            $data['opening_bank']=I('opening_bank');
            if($data['opening_bank']=='' ||  $data['phone']==''){
                $this->ajaxReturn(V(0,'信息填写不完整'));
            }
            $userinfo_account  =$accountModel->where(array('user_id='.$user_id,'opening_bank'=>$data['opening_bank'],'cardnum'=>I('cardnum')))->find();
            if($userinfo_account){
                $this->ajaxReturn(V(0,'该账号已添加'));
            }

            $this->checkbank($userinfo['id_card'],I('name'),$data['cardnum']);
        }else{
            $userinfo_account  =$accountModel->where(array('user_id='.$user_id,'cardnum'=>$data['cardnum']))->find();
            if($userinfo_account){
                $this->ajaxReturn(V(0,'该账号已添加'));
            }
        }
        $result = $accountModel->add($data);
        if($result !== false){
            $this->ajaxReturn(V(1, '添加成功'));
        }else{
            $this->ajaxReturn(V(0, '添加失败'));
        }
    }

    //根据开户名,身份证,银行卡验证银行卡信息
    public function checkbank($id_card,$name,$idNo){
           // 判断之前是否提交过
            $where_card['name']=$name;
            $where_card['id_card']=$id_card;
            $where_card['cardNo']=$idNo;
            $where_card['content']=array('neq','');
            $res=M('BankCardData')->where($where_card)->find();
            if($res){
                $result=$res['content'];
            }else{
                $result=$this->checkBankCard($id_card,$name,$idNo);

                $add_data['name']=$name;
                $add_data['id_card']=$id_card;
                $add_data['cardNo']=$idNo;
                $add_data['add_time']=time();
                $add_data['content']=$result;
                M('BankCardData')->add($add_data);
               // p($result);die;
            }
            $result=json_decode($result,true);
            if($result){
                if($result['respCode']!='0000'){
                    $this->ajaxReturn(V(0, $result['respMessage']));
                }
            }else{
                $this->ajaxReturn(V(0, "网络错误,请稍后重试"));
            }
    }

    //调用接口判断银行卡
    public function checkBankCard($id_card,$name,$idNo){
        $host="https://yunyidata3.market.alicloudapi.com";
        $path="/bankAuthenticate3";
        $querys="cardNo={$id_card}&name={$name}&idNo={$idNo}";
        $appcode = C('Aliyun')['AppCode'];
        $get=postForm($host,$path,$appcode,$querys);
        return $get;
    }


    //提现账号列表
    public function getAccountNumber(){
        $type             = I('type');
        $where['type']    = $type;
        $where['user_id'] = UID;
        $accountModel=M('AccountNumber');
        if($type==1){
            $field="id,cardnum,name";
        }else{
            $field="id,cardnum,name,phone,opening_bank";
        }
        $numberinfo  =$accountModel->where($where)->field($field)->select();
        if($numberinfo){
            $this->ajaxReturn(V(1, '账号列表',$numberinfo));
        }else{
            $this->ajaxReturn(V(1, '暂无提现账号'));
        }

    }


    //修改登录密码
    public function editpassword(){
        $user_id = UID;
        $oldpassword = I('oldpassword');
        $newpassword = I('newpassword');
        $newpassword_confirm =I('newpassword_confirm');
        $user = M('User');
        if($oldpassword==''){
            $this->apiReturn(0, '请输入旧密码');
        }
        if($newpassword==''){
            $this->apiReturn(0, '请输入新密码');
        }
        if($newpassword!=$newpassword_confirm){
            $this->apiReturn(0, '两次输入的新密码不正确');
        }
        $oldpassword =md5( I('oldpassword'));
        $newpassword = md5(I('newpassword'));
        $newpassword_confirm = md5(I('newpassword_confirm'));
        $userinfo = $user->where('id='.$user_id)->find();
        if($userinfo['password'] && $userinfo['password'] != $oldpassword){
            $this->apiReturn(0, '原密码不正确');
        }
        $update_data['password'] = $newpassword;
        $result = $user->where('id='.$user_id)->save($update_data);
        if($result !== false){
            $this->apiReturn(1, '修改密码成功');
        }else{
            $this->apiReturn(0, '修改密码失败');
        }
    }


    //修改支付密码
    public function editzfpassword(){
        $user_id = UID;
        $paypassword =md5( I('password'));
        $code =  I('code');
        $sendcode =  session('zfphone_code');
        $phone = session('zfphone_number');
        if($code != $sendcode){
            $this->apiReturn(0, '验证码不正确');
        }

        $user = M('User');
        $userinfo = $user->where('id='.$user_id)->find();

        if($userinfo['phone'] == ''){
            $update_data['phone']  =  $phone;
        }else{
            if($userinfo['phone'] != $phone){
                $this->apiReturn(0, '手机号码不正确');
            }
        }

        $update_data['pay_password'] = $paypassword;
        $result = $user->where('id='.$user_id)->save($update_data);
        if($result !== false){
            $this->apiReturn(1, '修改支付密码成功');
        }else{
            $this->apiReturn(0, '修改支付密码失败');
        }
    }


    //实名认证
    public function authentication(){
        $user_id = UID;
        $user = M('User');
        $append['name'] =I('real_name');
        $append['id_card'] = I('id_card');
        $append['id_card_phone_upon']=I('id_card_phone_upon');
        $append['id_card_phone_down']=I('id_card_phone_down');
        $append['id_card_phone_hold']=I('id_card_phone_hold');
        $append['is_permission'] = 2;
        //$result = $user->where('id='.$user_id)->save($append);
        $data = D('Home/User');
        if($data->create($append) !== false){
            if ($user_id > 0) {
                $this->checkIdCard(I('real_name'),I('id_card'));//验证身份证号
                $r=$data->where('id='. $user_id)->save($append);

            }
            $this->ajaxReturn(V(1, '提交成功'));
        } else {
            $this->ajaxReturn(V(0, $data->getError()));
        }
    }
    //验证身份证号
    public function checkIdCard($name,$id_card){
        if($name && $id_card){
            //判断之前是否提交过
            $where_card['name']=$name;
            $where_card['id_card']=$id_card;
            $where_card['content']=array('neq','');
            $res=M('CardData')->where($where_card)->find();
            if($res){
                $result=$res['content'];
            }else{
                $result=$this->checkCard($name,$id_card);
                $add_data['name']=$name;
                $add_data['id_card']=$id_card;
                $add_data['add_time']=time();
                $add_data['content']=$result;
                M('CardData')->add($add_data);
            }
            $result=json_decode($result,true);
            if($result['error_code']==0){
                if($result['result']['isok']=='' || $result['result']['isok']==false){
                    $this->ajaxReturn(V(0, '身份证号与真实姓名不匹配'));
                }
            }else{
                $this->ajaxReturn(V(0, '认证中心库中无此身份证记录'));
            }
        }else{
            $this->ajaxReturn(V(0, '信息提交不完整'));
        }
    }

    //验证身份证号
    public function checkCard($name,$id_card){
        $host="http://aliyunverifyidcard.haoservice.com";
        $path="/idcard/VerifyIdcardv2";
        $querys="cardNo={$id_card}&realName={$name}";
        $appcode = C('Aliyun')['AppCode'];
        $get=getForm($host,$path,$appcode,$querys);
        return $get;
    }

    //绑定手机
    public function editphone(){
        $user_id = UID;
        $phone =I('mobile');
        $code = I('code');
        $type = I('type');
        $user = M('User');
        // 验证验证码
        $re_code = A('Api/PublicApi')->checkPhoneVerify($code,$phone);
       // p($re_code);die;
        if (!$re_code) {
            $this->apiReturn(0, '验证码错误或已过期！');
        }

//        $phone_code = session('phone_code');
//        $phone_number = session('phone_number');
//
//        if($code != $phone_code){
//            $this->ajaxReturn(V(0, '验证码不正确'));
//        }
//        if($phone != $phone_number){
//            $this->ajaxReturn(V(0, '手机号码不正确'));
//        }
        $count = $user->where('phone='.$phone)->count();
        if($count > 0){
            $this->ajaxReturn(V(0, '该手机已经绑定帐号'));
        }
        $update_data['phone'] = $phone;
        $result = $user->where('id='.$user_id)->save($update_data);
        if($result !== false){
            $this->ajaxReturn(V(1, '绑定手机成功'));
        }else{
            $this->ajaxReturn(V(0, '绑定手机失败'));
        }
    }


    //卖卡记录
    public function sellrecord(){
        LL(I(''));
        $user_id            = UID;
        $start_time         = I('start_time');//开始时间
        $end_time           = I('end_time');//结束时间
        $order_type         = I('order_type');//卖卡状态  1:等待验证 2:完成交易 3:交易失败
        $card_type          = I('card_type');//卡种类型
        $order_keyword_type = I('order_keyword_type');//订单查询分类 1.订单号 2:卡号 3:卡密
        $order_keyword      = I('order_keyword');//订单查询关键词
        $time_type          = I('time_type');//1:今天 2:最近7天 3:1个月 4:2个月 5:3个月 6:1年


        $start_time_strtotime=strtotime($start_time);
        $end_time_strtotime=strtotime($end_time);
        //开始时间与结束时间
        if($start_time && $end_time){
            $where['add_time'] =array('between',array($start_time_strtotime, $end_time_strtotime));
        }elseif(!$start_time && $end_time){
            $where['add_time'] =array('lt',$end_time_strtotime);
        }elseif($start_time && !$end_time){
            $where['add_time'] =array('gt',$start_time_strtotime);
        }
        //卖卡状态
        if($order_type){
            $where['order_status']=$order_type;
        }
        //订单查询分类
        $salerecord =M('SaleRecord');
        if($order_keyword){
            if($order_keyword_type==1){
                $where['order_sn']=array('like',"%".$order_keyword."%");
            }elseif($order_keyword_type==2){
                $where_card['cardkey']=array('like',"%".$order_keyword."%");
                $arr_orders=$salerecord->where($where_card)->getField('order_id',true);
                $where['order_id']=array('in',implode(',',$arr_orders));
            }elseif($order_keyword_type==3){
                $where_card['password']=array('like',"%".$order_keyword."%");
                $arr_orders=$salerecord->where($where_card)->getField('order_id',true);
                $where['order_id']=array('in',implode(',',$arr_orders));
            }
        }

        //卡种类型
        if($card_type){
            $where['card_id']=$card_type;
        }
        //时间查询
        $array_time_select=array('2'=>7,'3'=>30,'4'=>60,'5'=>90,'6'=>365);
        $end_select_time = strtotime(date('Y-m-d 23:59:59'));
        if($time_type==1){
            $start_select_time = strtotime(date('Y-m-d 00:00:00'));
        }
        $date_select=$array_time_select[$time_type];//天数
        if($date_select){
            $start_select_time= time() - $date_select*24*3600;
        }
        if($start_select_time && $end_select_time){
            $where['add_time'] =array('between',array($start_select_time, $end_select_time));
        }
        //p($where);die;
$where['user_id']=UID;
        $orderModel=M('Order');
        $p=I('p',1);
        $ordersninfo = $orderModel
            //->where('user_id='.$user_id)
            ->where($where)
            ->field('order_sn,add_time,card_name,order_status,order_amount,total_amount,order_id')
            ->order('order_id desc')
            ->page($p,6)
            ->select();

        foreach($ordersninfo as $k=>$v){
            $ordersninfo[$k]['add_time']=date('Y-m-d H:i',$v['add_time']);
            if($v['order_status']==1){
                $ordersninfo[$k]['order_status_name']="待验证";
            }elseif($v['order_status']==2){
                $ordersninfo[$k]['order_status_name']="交易成功";
            }else{
                $ordersninfo[$k]['order_status_name']="交易失败";
            }
        }
        $this->ajaxReturn(V(1, '卖卡记录',$ordersninfo));
    }


    //卖卡记录详情
    public function sellrecord_detail(){
        $order_id=I('order_id');
        $where['order_id']=$order_id;
        //获取主订单信息
        $orderModel=M('Order');
        $orderinfo = $orderModel->where($where)->field('order_sn,add_time,order_status,order_amount,end_time')->find();
        if($orderinfo['add_time']){
            $orderinfo['add_time']=date('Y-m-d H:i:s',$orderinfo['add_time']);
        }
        if($orderinfo['end_time']){
            $orderinfo['end_time']=date('Y-m-d H:i:s',$orderinfo['end_time']);
        }else{
            $orderinfo['end_time']='';
        }
        $salerecord =M('SaleRecord');
        $orderinfo['count']=$salerecord->where($where)->count();//提交的总卡数
        $where_suc['static'] = 1;
        $orderinfo['sucesscount']=$salerecord->where($where)->where($where_suc)->count();//提交成功的
        unset($where_suc);
        $orderinfo['falsecount'] = $orderinfo['count'] - $orderinfo['sucesscount'];//提交失败的
        //订单下的每个卡记录
        $cardList = $salerecord->where($where)->field('price,saleprice,actual_price,flag_time,flag,is_entitycard,imgs,cardkey,password,card_name,static,remarks')->select();
       foreach($cardList as $key=>$v){
           if($v['flag_time']){//审核时间处理
               $cardList[$key]['flag_time']=date('Y-m-d H:i:s',$v['flag_time']);
           }else{
               $cardList[$key]['flag_time']='';
           }
           if($v['imgs']){//实体卡图片处理
               $cardList[$key]['imgs']=MOBILE_URL.$v['imgs'];
           }else{
               $cardList[$key]['imgs']='';
           }

           if($v['flag']==2){//0卡密不正确 1全部正确 2 金额不正确'
                if($v['static']==1){
                    $cardList[$key]['order_status'] = "验证成功";
                }else{
                    if($v['remarks']){
                        $cardList[$key]['order_status'] = $v['remarks'];
                    }else{
                        $cardList[$key]['order_status']="验证失败";
                    }
                }
           }else{
               $cardList[$key]['order_status'] = "待审核";
           }

       }
        $result['order']=$orderinfo;
        $result['orderList']=$cardList;
        $this->ajaxReturn(V(1, '卖卡记录',$result));

    }


    /*
      * 提现
      */
    public function withdrawals() {
        $type=I('type',1);//1支付宝 2 银行卡
        //查询用户的个人信息
        $user = M('User');
        $user_info = $user->where('id='.UID)->field('name,is_permission,balance')->find();
        $where['type']=$type;
        $where['user_id']=UID;
        $accountNumber=M('AccountNumber');
        $account=$accountNumber->where($where)->order('id desc')->field('type,id,cardnum')->find();
        $list=array_merge($user_info,$account);
        if($list==''){
            $list=array();
        }
        $this->ajaxReturn(V(1, '个人信息',$list));

    }


    //提现申请
    function withcash_apply(){
       // $password = md5(I('paypassword'));
        $userinfo  =M('User')->where('id='.UID)->find();
        $map['user'] = UID;
        $map['flag'] = 1;
        $waitcashmoney = M('PaymentRecord')->where($map)->sum('pay_cash');
        //$waitcashmoney = $waitcashmoney + I('money');
        //$waitcashmoney=I('money');
        if($waitcashmoney>0){
            $this->ajaxReturn(V(0,'您还有未审核的提现'));
        }
        if($userinfo['balance'] < $waitcashmoney) {
            $this->ajaxReturn(V(0,'余额不足'));
        }
//        $oldpassword =$userinfo['pay_password'];
//        if($oldpassword == ''){
//            $data['pay_password'] = $password;
//            M('User')->where('id='.UID)->save($data);
//            unset($data);
//        }else if($oldpassword != $password){
//            $this->ajaxReturn(V(0,'密码不正确'));
//        }
        if($userinfo['name'] == ''||$userinfo['is_permission'] != 1){
            $this->ajaxReturn(V(0,'请先实名认证'));
        }
        //查询提现的账号是否正确
        $account_id=I('account_id');
        $where_account['id']    = $account_id;
        $where_account['type']  = I('paytype',1);
        $where_account['user_id']  = UID;
        $accountInfo = M('AccountNumber')->where($where_account)->find();
        if(!$accountInfo){
            $this->ajaxReturn(V(0,'提现账号有误'));
        }
        $data['member_name'] = $userinfo['name'];
        $data['account_id']  = $account_id;
        $data['alipaycount'] = $accountInfo['cardnum'];
        $data['pay_cash']    = I('money');
        $data['falg_type']   = I('paytype');
        $data['bankname']   = $accountInfo['opening_bank'];
        //默认支付宝提现
        if($data['falg_type'] != 2){
            $data['falg_type'] = 1;
        }
        if($data['pay_cash'] < 1) {
            $this->ajaxReturn(V(0,'提现金额输入错误'));
        }
        $data['add_time'] = time();
        $data['balance']=$userinfo['balance'];
        $data['user'] = UID;
        $data['sale_record_id'] =date('YmdHis').rand(1000,9999);
        $result =  M('PaymentRecord')->add($data);
        if($result){
            $save['balance']=$userinfo['balance']-$data['pay_cash'];
            $result =  M('User')->where(array('id'=>UID))->save($save);
            $this->ajaxReturn(V(1,'申请成功'));
        }

    }


    /*
     * 提现历史
     */
    public function withdrawalshistory() {
        $start_time         = I('start_time');//开始时间
        $end_time           = I('end_time');//结束时间
        $time_type          = I('time_type');//1:今天 2:最近7天 3:1个月 4:2个月 5:3个月 6:1年

        $start_time_strtotime=strtotime($start_time);
        $end_time_strtotime=strtotime($end_time);
        //开始时间与结束时间
        if($start_time && $end_time){
            $where['add_time'] =array('between',array($start_time_strtotime, $end_time_strtotime));
        }elseif(!$start_time && $end_time){
            $where['add_time'] =array('lt',$end_time_strtotime);
        }elseif($start_time && !$end_time){
            $where['add_time'] =array('gt',$start_time_strtotime);
        }
        //时间查询
        $array_time_select=array('2'=>7,'3'=>30,'4'=>60,'5'=>90,'6'=>365);
        $end_select_time = strtotime(date('Y-m-d 23:59:59'));
        if($time_type==1){
            $start_select_time = strtotime(date('Y-m-d 00:00:00'));
        }
        $date_select=$array_time_select[$time_type];//天数
        if($date_select){
            $start_select_time= time() - $date_select*24*3600;
        }
        if($start_select_time && $end_select_time){
            $where['add_time'] =array('between',array($start_select_time, $end_select_time));
        }
        $where['user']=UID;
        $paymentList = M('PaymentRecord')->where($where)->field('id,add_time,pay_cash,alipaycount,static,falg_type,flag,remarks')->order('id desc')->select();

        foreach($paymentList as $key=>$v){
            $paymentList[$key]['add_time']=date('Y-m-d H:i',$v['add_time']);
            if($v['falg_type']==1){
                if(isMobile($v['alipaycount'])===true){
                    $paymentList[$key]['alipaycount']=substr_replace($v['alipaycount'],'*****',3,6);
                }
                if(isEmail($v['alipaycount'])===true){
                    $arr = explode('@', $v['alipaycount']);
                    $rest = substr($arr[0], 0, -2);
                    $arr[0] = str_replace($rest, str_repeat('*', strlen($rest)), $arr[0]);
                    $paymentList[$key]['alipaycount']=$arr[0].'@'.$arr[1];
                }
            }elseif($v['falg_type']==2){
                $paymentList[$key]['alipaycount']="尾号".substr($v['alipaycount'],-4);
            }
            if($v['flag']==1){
                $paymentList[$key]['flag_status']="待审核";
            }else{
                if($v['static']==1){
                    $paymentList[$key]['flag_status']="审核失败";
                }else{
                    $paymentList[$key]['flag_status']="转账完成";
                }
            }
        }
       // p($paymentList);die;
        //$this->ajaxReturn(V(1,'提现记录',$paymentList));
        $this->ajaxReturn(V(1,'提现记录',$paymentList));
    }

    //我提交过的卡
    public function get_submit_card(){
        LL(I(''));
        $salerecord =D('Home/SaleRecord');
        $where['user_id']=UID;
        $where['card_id']=array('neq',0);
        $field="c.card_logo,c.id";
        $cardList=$salerecord-> getCardList($where, $field);
        if($cardList){
            $this->ajaxReturn(V(1,'我提交过的卡',$cardList));
        }else{
            $this->ajaxReturn(V(1,'我提交过的卡',array()));
        }

    }



    //修改邮箱
    public function editemail(){
        $email =I('email');
        $code = I('code');
        $user = M('User');
        // 验证验证码
        $re_code = A('Api/PublicApi')->checkEmailVerify($code,$email);
        if (!$re_code) {
            $this->apiReturn(0, '验证码错误或已过期！');
        }
        $count = $user->where('email='.$email)->count();
        if($count > 0){
            $this->ajaxReturn(V(0, '该邮箱已经绑定其它帐号'));
        }
        $update_data['email'] = $email;
        $result = $user->where('id='.UID)->save($update_data);
        if($result !== false){
            $this->ajaxReturn(V(1, '绑定邮箱成功'));
        }else{
            $this->ajaxReturn(V(0, '绑定邮箱失败'));
        }
    }


    //操作日志
    public function get_action_log(){
        $p=I('p',1);
        $where['user_id']=UID;
        $list=M('ActionLog')->where($where)->order('id desc')->page($p,10)->select();
        foreach($list as $key=>$v){
            $list[$key]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
        }
        if($list){
            $this->ajaxReturn(V(1, '操作日志',$list));
        }else{
            $this->ajaxReturn(V(1, '操作日志',array()));
        }


    }


    //修改登录名
    public function editqq(){
        $qq =I('qq');
        $user = M('User');
        if (!$qq) {
            $this->apiReturn(0, '请输入qq！');
        }
        $update_data['qq'] = $qq;
        $result = $user->where('id='.UID)->save($update_data);
        if($result !== false){
            $this->ajaxReturn(V(1, '设置成功'));
        }else{
            $this->ajaxReturn(V(0, '设置失败'));
        }
    }

    //判断是否实名认证
    public function is_authentication(){
        $user=M('User');
        $user_info = $user->where('id='.UID)->field('is_permission')->find();
        if($user_info){
            $this->ajaxReturn(V(1, '实名认证',$user_info));
        }else{
            $this->ajaxReturn(V(0, '暂无记录'));
        }
    }
    //删除账号信息
    public function del_account(){
        $account_id=I('account_id');
        $where['id']=$account_id;
        $where['user_id']=UID;
        $model=M("AccountNumber");
        $info=$model->where($where)->find();
        if($info){
            $r=$model->where($where)->delete();
            if($r !== false){
                $this->ajaxReturn(V(1, '删除成功'));
            }else{
                $this->ajaxReturn(V(0, '删除失败'));
            }
        }else{
            $this->ajaxReturn(V(0, '不是您的账号,无权删除'));
        }
    }

    //修改登录名
    public function editlogin_account(){
        $login_account =I('login_account');
        $user = M('User');
        if (!$login_account) {
            $this->apiReturn(0, '请输入登录名！');
        }
        //判断是之前是否存在过
        $info=$user->where(array('login_account'=>$login_account))->find();
        if($info){
            $this->ajaxReturn(V(0, '该账号已被使用'));
        }
        $update_data['login_account'] = $login_account;
        $update_data['is_modify_username'] =1;
        $result = $user->where('id='.UID)->save($update_data);
        if($result !== false){
            $this->ajaxReturn(V(1, '设置成功'));
        }else{
            $this->ajaxReturn(V(0, '设置失败'));
        }
    }

    //导出
    public function export(){
        $user_id            = UID;
        $start_time         = I('start_time');//开始时间
        $end_time           = I('end_time');//结束时间
        $order_type         = I('order_type');//卖卡状态  1:等待验证 2:完成交易 3:交易失败
        $card_type          = I('card_type');//卡种类型
        $order_keyword_type = I('order_keyword_type');//订单查询分类 1.订单号 2:卡号 3:卡密
        $order_keyword      = I('order_keyword');//订单查询关键词
        $time_type          = I('time_type');//1:今天 2:最近7天 3:1个月 4:2个月 5:3个月 6:1年


        $start_time_strtotime=strtotime($start_time);
        $end_time_strtotime=strtotime($end_time);
        //开始时间与结束时间
        if($start_time && $end_time){
            $where['add_time'] =array('between',array($start_time_strtotime, $end_time_strtotime));
        }elseif(!$start_time && $end_time){
            $where['add_time'] =array('lt',$end_time_strtotime);
        }elseif($start_time && !$end_time){
            $where['add_time'] =array('gt',$start_time_strtotime);
        }
        //卖卡状态
        if($order_type){
            $where['order_status']=$order_type;
        }
        //订单查询分类
        $salerecord =M('SaleRecord');
        if($order_keyword){
            if($order_keyword_type==1){
                $where['order_sn']=array('like',"%".$order_keyword."%");
            }elseif($order_keyword_type==2){
                $where_card['cardkey']=array('like',"%".$order_keyword."%");
                $arr_orders=$salerecord->where($where_card)->getField('order_id',true);
                $where['order_id']=array('in',implode(',',$arr_orders));
            }elseif($order_keyword_type==3){
                $where_card['password']=array('like',"%".$order_keyword."%");
                $arr_orders=$salerecord->where($where_card)->getField('order_id',true);
                $where['order_id']=array('in',implode(',',$arr_orders));
            }
        }

        //卡种类型
        if($card_type){
            $where['card_id']=$card_type;
        }
        //时间查询
        $array_time_select=array('2'=>7,'3'=>30,'4'=>60,'5'=>90,'6'=>365);
        $end_select_time = strtotime(date('Y-m-d 23:59:59'));
        if($time_type==1){
            $start_select_time = strtotime(date('Y-m-d 00:00:00'));
        }
        $date_select=$array_time_select[$time_type];//天数
        if($date_select){
            $start_select_time= time() - $date_select*24*3600;
        }
        if($start_select_time && $end_select_time){
            $where['add_time'] =array('between',array($start_select_time, $end_select_time));
        }
        //p($where);die;
        $where['user_id']=UID;
        $orderModel=M('Order');
        $p=I('p',1);
        $ordersninfo = $orderModel
            //->where('user_id='.$user_id)
            ->where($where)
            ->field('order_sn,add_time,card_name,order_status,order_amount,total_amount,order_id')
            ->order('order_id desc')
            ->select();

        foreach($ordersninfo as $k=>$v){
            $ordersninfo[$k]['add_time']=date('Y-m-d H:i',$v['add_time']);
            if($v['order_status']==1){
                $ordersninfo[$k]['order_status_name']="待验证";
            }elseif($v['order_status']==2){
                $ordersninfo[$k]['order_status_name']="交易成功";
            }else{
                $ordersninfo[$k]['order_status_name']="交易失败";
            }
        }
        $this->ajaxReturn(V(1, '卖卡记录',$ordersninfo));

    }







}

