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
        $user_id = UID;
        $typeid = I('typeid');
        $cardid = I('cardid',0,'intval');//卡种
        $priceid = I('priceid',0,'htmlspecialchars');//卡片价格,自定义的话为zdy,否则为价格id
        $submissionid =  I('submission',0,'intval');//提交方式  1批量 2单卡
        $cardkey = I('cardkey','','htmlspecialchars');//卡号卡密
        $custom = I('custom',0,'intval');//自定义价格
        $express = I('express',0,'intval');//是否加急
        $is_entitycard = I('is_entitycard',0,'intval');//是否是实体卡  0否 1是
        if($cardid == ''){
            $this->apiReturn(0,'请选择卡片种类');
        }
        if($priceid == ''){
            $this->apiReturn(0,'请选择卡片价格');
        }
        $sale_record = M('SaleRecord');
        $data['card_id'] = $cardid;
        $data['card_price_id'] = $priceid;
        $data['user_id'] = $user_id;
        $data['add_time'] = time();
        $data['express'] = $express;
        $data['flag'] = 1;
        $data['submission_type'] = $submissionid;
        $data['order_sn'] = 'S'.date('mdHis').$priceid.rand(1000,9999);

        if($priceid == 'zdy'){
            $data['price'] = $custom;
            $sale_propo = M('Card')->where('id='.$cardid)->getField('sale_proportion');
            $data['saleprice'] = $custom * $sale_propo;
        }else{
            $pricedatainfo = M('CardPrice')->where('id='.$priceid)->find();
            $data['price'] = $pricedatainfo['price'];
            $data['saleprice'] = $pricedatainfo['sale_price'];
        }
        if($data['express'] == 1){$data['saleprice'] = $data['saleprice'] * 0.97;}
        if($submissionid == 1){
            if($is_entitycard==0){//非实体卡
                $cardarray=explode(",",$cardkey);
                foreach($cardarray as $val) {
                    $cardkeyarray=explode(" ",$val);
                    $data['cardkey'] = $cardkeyarray[0];
                    $data['password'] = $cardkeyarray[1];
                    if($data['cardkey'] == ''){
                        continue;
                    }
                    $result = $sale_record->add($data);

                }
            }else{//实体卡
                $comment_img = explode(',', I('imgs'));
                $img_count = count($comment_img);
                if($img_count > 10){
                    $this->apiReturn(0, '上传图片不能超过10张！');
                }
                foreach($comment_img as $k=>$v){
                    $data['imgs']=$v;
                    $result = $sale_record->add($data);
                }

            }

        }else if($submissionid == 2){
            $cardkeyarray=explode(" ",$cardkey);
            $data['cardkey'] = $cardkeyarray[0];
            $data['password'] = $cardkeyarray[1];
            if($data['cardkey'] != ''){
                $result = $sale_record->add($data);
            }
        }
        if( $result!=0){
            $this->apiReturn(1,'提交成功');
        }
    }




}

