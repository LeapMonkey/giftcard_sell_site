<?php

namespace Api\Controller;

/**
 * Copyright (c) 山东六牛网络科技有限公司 https://liuniukeji.com
 *
 * @Description    卖卡操作
 * @Author         zhanglili  qq:1640054073
 * @Copyright      Copyright (c) 山东六牛网络科技有限公司 保留所有版权(https://www.liuniukeji.com)
 * @Date           2019.4.23
 * @CreateBy       PhpStorm
 */
class CardCenterApiController extends CommonUserApiController
{
    //我要卖卡
    public function recoverycard(){

        LL(I(''));
        $user_id = UID;
        $typeid = I('typeid');
        $cardid = I('cardid',0,'intval');//卡种
        $priceid = I('priceid',0,'htmlspecialchars');//卡片价格,自定义的话为zdy,否则为价格id
        $submissionid =  I('submission',0,'intval');//提交方式  1批量 2单卡
        $cardkey = I('cardkey','','htmlspecialchars');//卡号卡密
        $custom = I('custom',0);//自定义价格
        $express = I('express',0,'intval');//是否加急
        // $is_entitycard = I('is_entitycard',0,'intval');//是否是实体卡  0否 1是
        $is_force = I('is_force',0,'intval');//是否强制提交(0否 1是)
        $user_id = UID;
        $user = M('User');
        $userinfo  =M('User')->where('id='.$user_id)->find();
//        if($userinfo['name'] == ''||$userinfo['is_permission'] != 1){
//            $this->ajaxReturn(V(0,'请先实名认证'));
//        }

        if($cardid == ''){
            $this->ajaxReturn(V(0,'请选择卡片种类'));
        }
        if($priceid == ''){
            $this->ajaxReturn(V(0,'请选择卡片价格'));
        }


        $order_card=M('Card')->where(array('id'=>$cardid))->find();
        $sale_record = M('SaleRecord');
        $data['card_id'] = $cardid;
        $data['type_id']=$typeid;
        $data['card_name'] = $order_card['name'];
        $data['user_id'] = $user_id;
        $data['add_time'] = time();
        $data['express'] = $express;
        $data['is_entitycard'] = $order_card['is_entitycard'];
        $data['submission_type'] = $submissionid;
        $data['order_sn'] = 'S'.date('mdHis').$priceid.rand(1000,9999);

        $result=$this->calculate_price($priceid,$cardid,$custom,$express);//计算价格

        $cardarray   =explode("\n",$cardkey);
        LL($cardarray);
        if($order_card['is_entitycard']==0){//判断是否是实体卡,非实体卡分批量提交和单张提交
            if($submissionid == 1){//判断是否是批量提交
                $cardarray   =explode("\n",$cardkey);
                $card_count   = count($cardarray);
            }else{
                $card_count=1;
            }
            if($card_count>100){
                $this->ajaxReturn(V(0, '最多只能提交100张！'));
            }
        }else{
            $img_all= rtrim(I('imgs'), ",") ;
            $comment_img = $cardkeyarray=explode(",",$img_all);
            $card_count   = count($comment_img);
            if($card_count>10){
                $this->ajaxReturn(V(0, '最多只能提交10张！'));
            }
        }

        //如果是非实体卡要验证卡的正确性
        if($order_card['is_entitycard']==0){
            $check_card=$this->checkCard($submissionid,$cardkey,$cardid);
            if($is_force==1){
                if($submissionid == 1){//判断是否是批量提交
                    if($check_card['status']==0) {
                        //p($check_card['result']['error_list']);die;
                        foreach($cardarray as $k=>$val) {
                            foreach ($check_card['result']['error_list'] as $ks =>$v) {
                                $ids=$v['id'];
                                unset($cardarray[$ids]);
                            }
                        }

                        $card_count   = count($cardarray);
                    }
                }else{
                    $card_count=0;
                }
                if($card_count==0){
                    $this->ajaxReturn(V(0, '没有提交有效的卡券！'));
                }

            }else{
                if($check_card['status']==0){//证明有不合格的卡券
                    $result_card=$check_card['result'];//错误卡券的数量以及哪个卡错误
                    $result_card['count']=$card_count;//总数量
                    //卡券有错误的时候,强制提交的情况

                    $this->ajaxReturn(V(0, '卡券有错误！',$result_card));
                }
            }
            // p($check_card);die;

        }
        //去除重复数据
//        $cardarray = second_array_unique_bykey($cardarray);
//        if($cardarray){
//            $card_count   = count($cardarray);
//        }

        $data['total_amount'] = $result['result']['actual_price'] * $card_count;//预计可得
        $data['order_amount'] = '0.00';//实际到账


        // 添加订单
        $order_id = M('order')->add($data);
        if($order_id)
        {
            $order['order_id'] = $order_id;
            $order['type_id'] = $typeid;
            $order['card_id'] = $cardid;
            $order['user_id'] = $user_id;
            $order['express'] = $express;//是否加急
            $order['flag'] = 1;//审核状态
            $order['card_price_id'] = $priceid;//价格id
            $order['price'] = $result['result']['price'];//面值
            $order['saleprice'] = $result['result']['saleprice'];//回收价
            $order['actual_price']=$result['result']['actual_price'];//实际价格
            $order['card_name'] = $order_card['name'];//卡名称
            $order['is_entitycard'] = $order_card['is_entitycard'];//是否是实体卡(0 否 1是)
            $order['offline'] = $order_card['offline'];//是否在线回收
            $order['sale_proportion'] = $order_card['sale_proportion'];//比例
            if($order_card['is_entitycard']==0){//非实体卡
                if($submissionid == 1){
                    /// $cardarray=explode("\n",$cardkey);
                    foreach($cardarray as $val) {
                        $cardkeyarray=explode(" ",$val);
                        if($order_card['openpassword']==1){//如果是卡号卡密都得输入的
                            $order['cardkey'] = $cardkeyarray[0];
                            $order['password'] = $cardkeyarray[1];
                        }else{//只需要输入卡密的
                            if($cardkeyarray[0]!='' && $cardkeyarray[1]!==''){//如果只输入卡密的既输入了卡号又输入了卡号
                                $order['cardkey'] = $cardkeyarray[0];
                                $order['password'] = $cardkeyarray[1];
                            }else{
                                $order['password'] = $cardkeyarray[0];
                            }
                        }
                        $result = $sale_record->add($order);

                    }
                }else{
                    $cardkeyarray=explode(" ",$cardkey);
                    if($cardkeyarray[0]!='' && $cardkeyarray[1]!==''){//如果只输入卡密的既输入了卡号又输入了卡号
                        $order['cardkey'] = $cardkeyarray[0];
                        $order['password'] = $cardkeyarray[1];
                    }else{
                        $order['password'] = $cardkeyarray[0];
                    }
                    $result = $sale_record->add($order);
                }

            }else{//实体卡
                $img_all= rtrim(I('imgs'), ",") ;
                $comment_img = $cardkeyarray=explode(",",$img_all);
                // p($comment_img);die;
                $img_count = count($comment_img);
                if($img_count > 10){
                    $this->ajaxReturn(V(0, '上传图片不能超过10张！'));
                }
                foreach($comment_img as $k=>$v){
                    $order['imgs']=$v;
                    $result = $sale_record->add($order);
                }
            }
        }else{
            $this->error('添加失败');
        }

        if($result!=0){
            $this->ajaxReturn(V(1,'提交成功'));
        }
    }



    public function test(){
        $a=array('123 444','222 333','555 666','777 888');
        $b=array('1'=>'12123','3'=>'错误');
        foreach($b as $k=>$v){
            unset($a[$k]);
        }
        p($a);
    }
    public function checkCard($submissionid,$cardkey,$cardid){
        $order_card=M('Card')->where(array('id'=>$cardid))->find();
        $sale_record = M('SaleRecord');
        $i=0;
        $arr_error=array();
        if($submissionid == 1){
            $cardarray=explode("\n",$cardkey);
            //return array('status'=>0,'msg'=>"有问题",'result'=>$cardarray); // 返回结果状态
            //$where_order['flag']=2;
            $arr_error=array();
            foreach($cardarray as $k=>$val) {
                $cardkeyarray=explode(" ",$val);
               iconv("gbk","utf8",$cardkeyarray);
               if($cardkeyarray[0]){
                   $cardkeyarray[0]=trim($cardkeyarray[0],"\n");
                   $cardkeyarray[0]=trim($cardkeyarray[0]," ");
                   $cardkeyarray[0]=trim($cardkeyarray[0],"\r\n");

               }
                if($cardkeyarray[1]){
                    $cardkeyarray[1]=trim($cardkeyarray[1],"\n");
                    $cardkeyarray[1]=trim($cardkeyarray[1]," ");
                    $cardkeyarray[1]=trim($cardkeyarray[1],"\r\n");
                }
                if($order_card['openpassword']==1){//如果是卡号卡密都得输入的
                    $where_order['cardkey']=$cardkeyarray[0];
                    $where_order['password']=$cardkeyarray[1];


                }else{//只需要输入卡密的
                    if($cardkeyarray[0]!='' && $cardkeyarray[1]!==''){//如果只输入卡密的既输入了卡号又输入了卡号
                        $where_order['cardkey']=$cardkeyarray[0];
                        $where_order['password']=$cardkeyarray[1];
                    }else{
                        $where_order['password']=$cardkeyarray[0];
                    }
                }
                if($order_card['openpassword']==1){
                    if($cardkeyarray[0] && (strlen($cardkeyarray[0])!=$order_card['card_length'])){
                        LL($cardkeyarray[0]."------".strlen($cardkeyarray[0]));
                        // $arr_error=array_merge($arr_error,array(array('id'=>$k,'error'=>'卡号位数应为'.$order_card['card_length'].'位')));
                        $arr_error[$k]["id"] = $k;
                        $arr_error[$k]["error"] = '卡号位数应为'.$order_card['card_length'].'位';
                        $i++; continue;
                    }
                }else{
                    if($cardkeyarray[1]==''){
                        if($cardkeyarray[0] && (strlen($cardkeyarray[0])!=$order_card['card_password_length'])){
                            LL($cardkeyarray[0]."!!!!!".strlen($cardkeyarray[0]));
                            $arr_error[$k]["id"] = $k;
                            $arr_error[$k]["error"] = '卡密位数应为'.$order_card['card_password_length'].'位';
                            //$arr_error=array_merge($arr_error,array(array('id'=>$k,'error'=>'卡密位数应为'.$order_card['card_password_length'].'位')));
                            $i++;  continue;
                        }
                    }

                }
                if($cardkeyarray[1] && (strlen($cardkeyarray[1])!=$order_card['card_password_length'])){
                    LL($cardkeyarray[1]."&&&&".strlen($cardkeyarray[1]));
                    $arr_error[$k]["id"] = $k;
                    $arr_error[$k]["error"] = '卡密位数应为'.$order_card['card_password_length'].'位';
                    //$arr_error=array_merge($arr_error,array(array('id'=>$k,'error'=>'卡密位数应为'.$order_card['card_password_length'].'位')));
                    $i++;  continue;
                }
                $a=$sale_record->where($where_order)->count();
                if($a>0){
                    $i++;
                    $arr_error[$k]["id"] = $k;
                    $arr_error[$k]["error"] = '卡券已有记录';
                    //$arr_error=array_merge($arr_error,array('id'=>$k,'error'=>'卡券已有记录'));
                }
            }
        }else{
            $cardkeyarray=explode(" ",$cardkey);
            if($cardkeyarray[0]!='' && $cardkeyarray[1]!==''){//如果只输入卡密的既输入了卡号又输入了卡号
                $where_order['cardkey']=$cardkeyarray[0];
                $where_order['password']=$cardkeyarray[1];
            }else{
                $where_order['password']=$cardkeyarray[0];
            }
            LL($cardkeyarray[0]); LL($cardkeyarray[1]);
            $a=$sale_record->where($where_order)->count();
            LL($a);
            if($a>0){
                $i++;
                //$arr_error=array_merge($arr_error,array(array('id'=>0,'error'=>'卡券已有记录')));
                $arr_error[0]["id"] = 0;
                $arr_error[0]["error"] = '卡券已有记录';
            }
        }
        $list['error_count']=$i;
        $list['error_list']=array_values($arr_error);
        if($i>0){
            return array('status'=>0,'msg'=>"有问题",'result'=>$list); // 返回结果状态
        }else{
            return array('status'=>1,'msg'=>"正常",'result'=>array()); // 返回结果状态
        }

    }


    //价格计算
    public function calculate_price($priceid,$cardid,$custom,$express){
        if($priceid == 'zdy'){
            $data['price'] = $custom;
            $sale_propo = M('Card')->where('id='.$cardid)->getField('zdy_proportion');
            $data['saleprice'] = $custom * $sale_propo;
        }else{
            $pricedatainfo = M('CardPrice')->where('id='.$priceid)->find();
            $data['price'] = $pricedatainfo['price'];
            $data['saleprice'] = $pricedatainfo['sale_price'];
        }
        if($express == 1){
            $data['actual_price'] = $data['saleprice'] * 0.97;
        }else{
            $data['actual_price'] = $data['saleprice'];
        }
        return array('status'=>1,'msg'=>"计算价钱成功",'result'=>$data); // 返回结果状态
    }







}

