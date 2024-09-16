<?php
namespace Home\Controller;
use Think\AjaxPage;
/**
 * 前台首页控制器
 */
class UserController extends \Common\Controller\CommonWebController  {

    public function Index(){

        //查询用户的个人信息
        $user = M('User');
        $user_id = UID;

        $user_info = $user->where('id='.$user_id)->find();
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


        $this->assign('perfectDegree', $perfectDegree); 
  
        //根据用户id获得用户默认的地址信息
        $where['user'] = $user_id;
        $where['flag'] = 1;
        $money = M('PaymentRecord')->where($where)->sum('pay_cash');
      //上次提现支付宝账号
        $map['falg_type'] = 1;
        $map['user'] = $user_id;
        $paymentrecord = M('PaymentRecord')->where($map)->order('add_time desc')->limit(1)->select();
        $default_alipay = $paymentrecord[0]['alipaycount'];

        $address_lists = get_user_address_list($user_id);
        $region_list = get_region_list();

        $this->assign('region_list',$region_list);
        $this->assign('money',$money);
        $this->assign('list',$address_lists[0]);

        $this->assign('default_alipay',$default_alipay);
        $this->assign('user_info',$user_info);
        $this->display();
    }

    public function user(){
        //查询用户的个人信息
        $user = M('User');
        $user_id =UID;
        $user_info = $user->where('id='.$user_id)->field('login_account,name,email,phone,is_permission,is_modify_username,balance,qq_avatar_thumb,weixin,login_account,username')->find();
        $data['phone'] = $user_info['phone'];
        $data['qq_openid'] = $user_info['qq_openid'];
        $data['email'] = $user_info['email'];
        $data['wx_unionid'] = $user_info['wx_unionid'];
        $data['pay_password'] = $user_info['pay_password'];
        $NumberNoEmpty = 0;
        foreach($data as $vo){
            if(!empty($vo)){
                $NumberNoEmpty++;
            }
        }
        // 资料完善度
        $perfectDegree = floor(($NumberNoEmpty/5)*100);
        $this->assign('perfectDegree', $perfectDegree);
        //公告
        $announcement=M('Announcement');
        $annoince=$announcement->order('id desc')->find();
        if($annoince){
            $annoince['add_time']=date('Y-m-d',$annoince['add_time']);
        }
        //获取提现中的金额
        $where['user'] = $user_id;
        $where['flag'] = 1;
        $money = M('PaymentRecord')->where($where)->sum('pay_cash');
        $user_info['withdrawal']=$money;
        //查询提现账号
        $accountModel=M('AccountNumber');
        $account_number=$accountModel->where(array('user_id'=>$user_id))->field('id,type,cardnum')->order('id desc')->find();
        $user_info['type']=$account_number['type'];
        $user_info['cardnum']=$account_number['cardnum'];
        //我提交的卡
        $salerecord =D('SaleRecord');
        $where_card['user_id']=UID;
        $where_card['card_id']=array('neq',0);
        $field="c.card_logo,c.id";
        $cardList=$salerecord->getCardList($where_card, $field);
        $this->assign('annoince',$annoince);
        $this->assign('cardList',$cardList);
        $this->assign('user_info',$user_info);
        $this->display();
    }

    //操作日志
    public function log(){
        $actionLogModel=D('ActionLog');
        $where['user_id']=UID;
        $actionLog=$actionLogModel->getActionLogByPage($where);
        //p($actionLog);die;
        $this->assign('actionLog',$actionLog['list']);
        $this->assign('page',$actionLog['page']);
        $this->display();
    }

    //我要卖卡
    public function sellCard(){
        //是否提供发票
        $invoice = C('INVOICE_SITE');

        $typeid = I('typeid');
        $cardtype = M('CardType');

        $cardtype_info = $cardtype->field('id,name,photo_path')->select();
        if($typeid == ''){
            $typeid = $cardtype_info[0]['id'];

        }
        $card = M('Card');
        $where['type_id']= $typeid;
//        $where['offline']= array('neq',1);
        $cardinfo = $card->field('id,name,photo_path,sale_proportion,introduce,openpassword,cardexample,zdy_proportion,type_id,offline,is_entitycard')->where($where)->select();
        $introduce = $cardinfo[0]['introduce'];
        $onlypass = $cardinfo[0]['openpassword'];
        $cardexample = $cardinfo[0]['cardexample'];
        $cardid = $cardinfo[0]['id'];
        $wheredata['card_id'] = $cardid;

        $userInfo=M("User")->where(array('id'=>UID))->find();
        $this->assign('user_info',$userInfo);
        $result_list = M('CardPrice')->where($wheredata)->select();
        $this->assign('sale_proportion',$cardinfo[0]['sale_proportion']);
        $this->assign('zdy_proportion',$cardinfo[0]['zdy_proportion']);
        $this->assign('result_list',$result_list);
        $this->assign('cardid',$cardid);
        $this->assign('cardexample',$cardexample);
        $this->assign('onlypass',$onlypass);
        $this->assign('cardpriceid',$result_list[0]['id']);
        $this->assign('cardinfo',$cardinfo);
        $this->assign('typeid',$typeid);
        $this->assign('introduce',$introduce);
        $this->assign('invoice',$invoice);
        $this->assign('cardtype_info',$cardtype_info);
        $this->display();
    }
    //卖卡记录
    public function sellCardRecord(){
        $user_id            = UID;
        $start_time         = I('start_time');//开始时间
        $end_time           = I('end_time');//结束时间
        $order_type         = I('order_type');//卖卡状态  1:等待验证 2:完成交易 3:交易失败
        $card_type          = I('card_type');//卡种类型
        $order_keyword_type = I('order_keyword_type');//订单查询分类 1.订单号 2:卡号 3:卡密
        $order_keyword      = I('order_keyword');//订单查询关键词
        $time_type          = I('time_type');//1:今天 2:最近7天 3:1个月 4:2个月 5:3个月 6:1年


        $start_time_strtotime=strtotime($start_time);
        $end_time_strtotime=strtotime($end_time." +24 hours");
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
                $where['order_sn']=array('like','%$order_keyword%');
            }elseif($order_keyword_type==2){
                $where_card['cardkey']=array('like','%$order_keyword%');
                $arr_orders=$salerecord->where($where_card)->getField('order_id',true);
                $where['order_id']=array('in',implode(',',$arr_orders));
            }elseif($order_keyword_type==3){
                $where_card['password']=array('like','%$order_keyword%');
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

        $orderModel=M('Order');
        //$p=I('p',1);
        $count=$orderModel
            ->where('user_id='.$user_id)
            ->where($where)
            ->field('order_sn,add_time,card_name,order_status,order_amount,total_amount,order_id')
            //->page($p,6)
            ->count();
        $page = get_page($count);
        $limit = $page['limit'];
        $ordersninfo = $orderModel
            ->where('user_id='.$user_id)
            ->where($where)
            ->field('order_sn,add_time,card_name,order_status,order_amount,total_amount,order_id')
            ->limit($limit)
            ->order('id desc')
            //->page($p,6)
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
        //获取卡种类型
        $card = M('Card');
        $where['offline']= array('neq',1);
//        $where['type_id']= $typeinfo[0]['id'];
        $cardinfo =$card->field('id,name,card_logo,openpassword,photo_path,cardexample,introduce,sale_proportion,card_length,card_password_length,is_entitycard,offline,openpassword')->where($where)->select();
        $this->assign('cardtype',$cardinfo);
        $this->assign('page',$page['page']);
        $this->assign('ordersninfo',$ordersninfo);
        $this->display();
    }
    //统计记录
    public function  statistics(){
        $this->display();
    }
    //资料管理
    public function data(){
       $type=I('type',1);
        $bank_type=I('bank_type',1);
        $this->assign('bank_type',$bank_type);
        $from_ac=I('from_action');
        $this->assign('from_action',$from_ac);
       $this->assign('type',$type);
       $this->display();
    }

    //我要提现
    public function meApply(){
        $user = M('User');
        $user_id = UID;
        $user_info = $user->where('id='.$user_id)->find();
        //获取最新的支付宝账号
        $accountNumber=M('AccountNumber');
        $type=I('bank_type');
        $account_id=I('account_id');
        if($type==1 && $account_id!=''){
            $account_alipay=$accountNumber->where(array('type'=>1,'user_id'=>UID,'id'=>$account_id))->order('id desc')->field('type,id,cardnum')->find();
        }else{
            $account_alipay=$accountNumber->where(array('type'=>1,'user_id'=>UID))->order('id desc')->field('type,id,cardnum')->find();
           // $account_alipay =array();
        }

        if($account_alipay['cardnum']){
            if(isMobile($account_alipay['cardnum'])===true){
                $account_alipay['cardnum']=substr_replace($account_alipay['cardnum'],'*****',3,6);
            }
            if(isEmail($account_alipay['cardnum'])===true){
                $arr = explode('@', $account_alipay['cardnum']);
                $rest = substr($arr[0], 0, -2);
                $arr[0] = str_replace($rest, "***", $arr[0]);
                $account_alipay['cardnum']=$arr[0].'@'.$arr[1];
            }
        }


        //获取最新的银行卡账号
        if($type==2 && $account_id!='') {
            $account_wechat = $accountNumber->where(array('type' => 2, 'user_id' => UID,'id'=>$account_id))->order('id desc')->field('type,id,cardnum')->find();
        }else{
            $account_wechat = $accountNumber->where(array('type' => 2, 'user_id' => UID))->order('id desc')->field('type,id,cardnum')->find();
           // $account_wechat =array();
        }
        if($account_wechat['cardnum']){
            $account_wechat['cardnum'] = "尾号" . substr($account_wechat['cardnum'], -4);
        }
        $this->assign('bank_type',$type);
        $this->assign('user_info',$user_info);
        $this->assign('account_wechat',$account_wechat);
        $this->assign('account_alipay',$account_alipay);
        $this->display('meapply');
    }

    function withcash_apply(){
        // $password = md5(I('paypassword'));
        $userinfo  =M('User')->where('id='.UID)->find();
        $map['user'] = UID;
        $map['flag'] = 1;
        $waitcashmoney = M('PaymentRecord')->where($map)->sum('pay_cash');
        //$waitcashmoney =  I('money');

        if($waitcashmoney>0){
            $this->ajaxReturn(V(0,'您还有未审核的提现'));
        }
        $waitcashmoney = $waitcashmoney + I('money');
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
//        if($data['falg_type'] != 2){
//            $data['falg_type'] = 1;
//        }
        if($data['pay_cash'] < 1) {
            $this->ajaxReturn(V(0,'提现金额输入错误'));
        }
        $data['add_time'] = time();
        $data['balance']=$userinfo['balance'];
        $data['user'] = UID;
        $data['sale_record_id'] =date('YmdHis').rand(1000,9999);
        //p($data);die;
        $result =  M('PaymentRecord')->add($data);
        if($result){
            $save['balance']=$userinfo['balance']-$data['pay_cash'];
            $result =  M('User')->where(array('id'=>UID))->save($save);
            $this->ajaxReturn(V(1,'申请成功'));
        }

    }

    //资料管理详情
    public function dataInfo(){
        $this->display();
    }
    //卖卡详情
    public function sellCardDetails(){
        $this->display();
    }

    //修改密码
    public function editpassword(){
        $user_id = UID;
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

    public function cardprices(){
        $id = I('id', 0, 'intval');

        $where['card_id'] = $id;
        $result_list = M('CardPrice')->where($where)->select();
        $card = M('Card');
        $wheredata['id']= $id;

        $sale_proportion = $card->where($wheredata)->find();
        $onlypass = $sale_proportion['openpassword'];
//        if($result_list == ''){
//            $this->ajaxReturn(V(0,'数据错误'));
//        }
       // $this->ajaxReturn(V(1,$sale_proportion,$result_list));
        $this->assign('onlypass',$onlypass);
        $this->assign('sale_proportion',$sale_proportion);
        $this->assign('result_list',$result_list);
        $this->display('ajax_more');
    }
    //实名验证
    public function real_name(){
        $userauth = session('user_auth');

        if(!$userauth){
            $this->redirect('Home/Index/index');

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

    public function sellrecord(){

       $this->display();
    }
    public function record(){
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
        $count = M('PaymentRecord')
            ->where($where)
            ->field('id,add_time,pay_cash,alipaycount,static,falg_type,flag,remarks')
            ->order('id desc')
            ->count();
        $page = get_page_new($count,10);
        //分页跳转的时候保证查询条件
        foreach($where as $key=>$val) {
            $page->parameter[$key]   =   urlencode($val);
        }

        $limit = $page['limit'];
        $paymentList = M('PaymentRecord')
            ->where($where)
            ->field('id,add_time,pay_cash,alipaycount,static,falg_type,flag,remarks')
            ->order('id desc')
            ->limit($limit)
            ->select();

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
        $this->assign('start_time',$start_time);
        $this->assign('end_time',$end_time);
        $this->assign('time_type',$time_type);
        $this->assign('paymentList',$paymentList);
        $this->assign('page', $page['page']);

       // $this->ajaxReturn(V(1,'提现记录',$paymentList));
        //p(M('PaymentRecord')->_sql());die;
        $this->display();
    }


    public function ajax_sellrecord(){
        $user_id            = UID;
        $start_time         = I('start_time');//开始时间
        $end_time           = I('end_time');//结束时间
        $order_type         = I('order_type');//卖卡状态  1:等待验证 2:完成交易 3:交易失败
        $card_type          = I('card_type');//卡种类型
        $order_keyword_type = I('order_keyword_type');//订单查询分类 1.订单号 2:卡号 3:卡密
        $order_keyword      = I('order_keyword');//订单查询关键词
        $time_type          = I('time_type');//1:今天 2:最近7天 3:1个月 4:2个月 5:3个月 6:1年


        $start_time_strtotime=strtotime($start_time);
        $end_time_strtotime=strtotime($end_time." +24 hours");
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

        $orderModel=M('Order');
        $where['user_id']=UID;
        $count = $orderModel

            ->where($where)
            ->field('order_sn,add_time,card_name,order_status,order_amount,total_amount,order_id')
            ->count();
        $page = new AjaxPage($count,10);
        //分页跳转的时候保证查询条件
        foreach($where as $key=>$val) {
            $page->parameter[$key]   =   urlencode($val);
        }
        $listRows = 10;
        $p = I('p',1);
        $firstRow = ($p-1)*$listRows;
        $show = $page->show_new();
        $ordersninfo = $orderModel
            ->where($where)
            ->field('order_sn,add_time,card_name,order_status,order_amount,total_amount,order_id')
            ->limit($firstRow.','.$listRows)
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
        $this->assign('page',$show);
        $this->assign('ordersninfo',$ordersninfo);
       // p($ordersninfo);die;
        $this->display('ajax_more_record');
    }


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
       // p($userinfo['name']); p(I('name'));die;
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
            $userinfo_account  =$accountModel->where(array('user_id='.$user_id,'opening_bank'=>$data['opening_bank']))->find();
            if($userinfo_account){
                $this->ajaxReturn(V(0,'该账号已添加'));
            }

           //$this->checkbank($userinfo['id_card'],$userinfo['name'],$data['cardnum']);
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
        $querys="cardNo={$idNo}&name={$name}&idNo={$id_card}";
        $appcode = C('Aliyun')['AppCode'];
        $get=postForm($host,$path,$appcode,$querys);
        return $get;
    }
    public function  uploadImgs(){
        $typeArr = array("jpg", "png", "gif", "jpeg");//允许上传文件格式
        //$mkdir_file_dir = mkdir("/Uploads/picture/Article/".date('Y-m-d',time),0777);
//        $leixing = I('get.leixing');
//        $bianhao = I('get.bianhao');
//
//        if ($leixing == 1){
//            $path= "Upload/10000/M-$bianhao/";
//        }else{
//            $path= "Upload/10000/Z-$bianhao/";
//
//        }
        $path="Uploads/".date('Y-m-d',time);
        //$path= mkdir("Uploads/".date('Y-m-d',time),0777);

        if (!is_dir($path)) if(!mkdir($path)){echo json_encode(array("error"=>"创建目录失败！")); exit;}// 如果不存在则创建
        if (isset($_POST)) {
            $name = $_FILES['file']['name'];
            $size = $_FILES['file']['size'];
            $name_tmp = $_FILES['file']['tmp_name'];
            if (empty($name)) {
                echo json_encode(array("error"=>"您还未选择图片"));
                exit;
            }
            $type = strtolower(substr(strrchr($name, '.'), 1)); //获取文件类型

            if (!in_array($type, $typeArr)) {
                echo json_encode(array("error"=>"请上传jpg,png或gif类型的图片！"));
                exit;
            }
            if ($size > (500 * 1024)) {
                echo json_encode(array("error"=>"图片大小已超过500KB！"));
                exit;
            }

            $pic_name = time() . rand(10000, 99999) . "." . $type;//图片名称
            $pic_url = $path . $pic_name;//上传后图片路径+名称

            if (move_uploaded_file($name_tmp, $pic_url)) { //临时文件转移到目标文件夹

                echo json_encode(array("error"=>"0","pic"=>$pic_url,"name"=>$pic_name)); exit;
            } else {
                echo json_encode(array("error"=>"上传有误，请检查服务器配置！")); exit;
            }
        }

    }


    public function sellrecord_detail(){
        $order_id=I('order_id');
        $where['order_id']=$order_id;
        //获取主订单信息
        $orderModel=M('Order');
        $orderinfo = $orderModel->where($where)->field('order_sn,add_time,order_status,order_amount,end_time,is_entitycard')->find();
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
        //待审核的;
        $where_wait['flag']=1;
        $orderinfo['waitcount']=$salerecord->where($where)->where($where_wait)->count();
        unset($where_suc);
        $orderinfo['falsecount'] = $orderinfo['count'] - $orderinfo['sucesscount']- $orderinfo['waitcount'];//提交失败的
        //订单下的每个卡记录
        $count = $salerecord
            ->where($where)
            ->field('price,saleprice,actual_price,flag_time,flag,is_entitycard,imgs,cardkey,password,card_name,static,remarks')
            ->count();
        $page = get_page_new($count,15);

        $limit = $page['limit'];
        $cardList = $salerecord
            ->where($where)
            ->field('price,saleprice,actual_price,flag_time,flag,is_entitycard,imgs,cardkey,password,card_name,static,remarks')
            ->limit($limit)
            ->select();
        foreach($cardList as $key=>$v){
            if($v['password']){
                $cardList[$key]['password']=substr_replace($v['password'],'***',0,3);
            }
            if($v['cardkey']){
                $cardList[$key]['cardkey']=substr_replace($v['cardkey'],'***',0,3);
            }
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
        $this->assign('orderinfo',$orderinfo);
        $this->assign('cardList',$cardList);
        $this->assign('page',$page['page']);
        $this->display();
        //$this->ajaxReturn(V(1, '卖卡记录',$result));

    }
    // 第三方平台登录
    public function oauth_login(){
        $type=I('get.type');
        import("Org.ThinkSDK.ThinkOauth");
        $sdk=\ThinkOauth::getInstance($type);
        redirect($sdk->getRequestCodeURL());
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
        $end_time_strtotime=strtotime($end_time." +24 hours");
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

        $orderModel=M('Order');
        $listRows = 10;
        $p = I('p',1);
        $firstRow = ($p-1)*$listRows;
        $ordersninfo = array();
        $ordersninfo = $orderModel
            ->where('user_id='.$user_id)
            ->where($where)
            ->field('order_id,order_sn,add_time,card_name,order_status,order_amount,total_amount,order_id')
           // ->limit($firstRow.','.$listRows)
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
            unset($ordersninfo[$k]['order_status']);
        }
       /*
        *    [order_id] => 47
            [order_sn] => S05111102355668346
            [add_time] => 2019-05-11 11:02
            [card_name] => 中国石化加油卡
            [order_status] => 1
            [order_amount] => 0.00
            [total_amount] => 2000.00
            [order_status_name] => 待验证
        * */
        $title_array = array('订单id','订单编号','时间','卡种','实际转账','预计可得','交易状态');
        array_unshift($ordersninfo, $title_array);
       // p($ordersninfo);die;
        $count = count($ordersninfo);
        if(!$ordersninfo){
            $ordersninfo=array();
        }
        if($order_type==2){
            create_xls($ordersninfo, '卖卡成功记录表', '卖卡成功记录表', '卖卡成功记录表',array('A','B','C','D','E','F','G'),$count);
        }else{
            create_xls($ordersninfo, '卖卡记录表', '卖卡记录表', '卖卡记录表',array('A','B','C','D','E','F','G'),$count);
        }

    }

    //删除图片
    public  function del_pic(){
        $pic_url 	=	I('post.');
        $purl 		=	".".$pic_url['purl'];
        //echo $purl;
        if (file_exists($purl)) {
            $result=unlink($purl);
            echo $result;
        }else{
            echo "不在";
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
        $where['type'] = $type;
        $where['add_time'] = array('EGT', NOW_TIME - C('SMS_EXPIRE_TIME') * 60);
        $smsMessage = M('SmsMessage');
        // 查询最新验证对应的主键id
        $id = $smsMessage->where(array('mobile' => $mobile))->max('id');
        $where['id'] = $id;
        if ($smsMessage->where($where)->find()) {
            return true;
        } else{
            return false;
        }
    }
    //绑定手机
    public function editphone(){
        $user_id = UID;
        $phone =I('mobile');
        $code = I('code');
        $type = I('type');
        $user = M('User');
        // 验证验证码
        //$re_code = $this->checkPhoneVerify($code,$phone,2);
        $re_code = A('Api/PublicApi')->checkPhoneVerify($code,$phone);
        // p($phone);die;
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
        $where_arr['phone']=$phone;
        $count = $user->where($where_arr)->count();
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
}