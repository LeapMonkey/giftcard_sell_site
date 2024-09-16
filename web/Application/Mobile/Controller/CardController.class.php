<?php
namespace Mobile\Controller;
use Think\Controller;
/**
 * Created by PhpStorm.
 * User: ddf
 * Date: 2017/6/16
 * Time: 上午10:11
 */
class CardController extends HomeCommonController{
    public function cardCenter(){
        //是否提供发票
        $invoice = C('INVOICE_SITE');
        $typeid = I('typeid');
        $cardtype = M('CardType');
        $typeinfo = $cardtype->select();
        $cardtype_info = $cardtype->getField('id',true);
        if($typeid == ''){
        $where['type_id']= array('in',$cardtype_info);
        }else{
            $where['type_id']= $typeid;
        }
        $card = M('Card');
        $where['offline']= array('neq',1);
        $where['type_id']= $typeinfo[0]['id'];
        $cardinfo = $card->field('id,name,openpassword,photo_path,cardexample,introduce')->where($where)->select();
        $cardid = $cardinfo[0]['id'];
        $wheredata['card_id'] = $cardid;
        $result_list = M('CardPrice')->where($wheredata)->select();
       

        $this->assign('sale_proportion',$cardinfo[0]['sale_proportion']);
        $this->assign('result_list',$result_list);
        $this->assign('cardid',$cardid);
        $this->assign('cardpriceid',$result_list[0]['id']);
        $this->assign('cardinfo',$cardinfo);
        $this->assign('typeid',$typeid);
        $this->assign('typeinfo',$typeinfo);
        $this->assign('introduce',$introduce);
        $this->assign('invoice',$invoice);
        $this->assign('cardtype_info',$cardtype_info);

        $this->display();
    }
    public function cardprices(){
        $id = I('id', 0, 'intval');
        $where['card_id'] = $id;
        $result_list = M('CardPrice')->where($where)->select();
        $card = M('Card');
        $wheredata['id']= $id;
        $sale_proportion = $card->where($wheredata)->getField('sale_proportion');
        if($result_list == ''){
            $this->ajaxReturn(V(0,'数据错误'));
        }
        $result_list = json_encode($result_list);
        $this->ajaxReturn(V(1,$sale_proportion,$result_list));
    }

    public function recoverycard(){
        $res_login = is_login();
        if($res_login == 0){
            $this->ajaxReturn(V(3,'请登录'));
            exit();
        }
        $user_id = $res_login['id'];
        $cardid = I('cardid',0,'intval');
        $priceid = I('priceid',0,'htmlspecialchars');
        $submissionid =  I('submission',0,'intval');
        $cardkey = I('cardkey','','htmlspecialchars');
        $custom = I('custom',0,'intval');
        $express = I('express',0,'intval');
        if($cardid == ''){
            $this->ajaxReturn(V(0,'请选择卡片种类'));
            exit();
        }
         if($priceid == ''){
            $this->ajaxReturn(V(0,'请选择卡片价格'));
            exit();
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
            $cardarray=explode("\n",$cardkey);
            foreach($cardarray as $val) {
                $cardkeyarray=explode(" ",$val);
                $data['cardkey'] = $cardkeyarray[0];
                $data['password'] = $cardkeyarray[1];
                if($data['cardkey'] == ''){
                    continue;
                }
                $result = $sale_record->add($data);

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
           $this->ajaxReturn(V(1,'提交成功'));
        }
    }
    public function conversion(){
        $this->display();
    }
    public function offline(){
        $this->display();
    }
    public function recycle(){
        $this->display();
    }
    public function sellrecord(){
        $res_login = is_login();
        $user_id = $res_login['id'];
        if($user_id == 0){
            $this->redirect('Mobile/User/login');
        }
        $salerecord =M('SaleRecord');
        $ordersninfo = $salerecord->where('user_id='.$user_id)->distinct(true)->field('order_sn,add_time')->select();
        $card = M('card');
        foreach($ordersninfo as  $key => $val) {
            $where['order_sn'] =$val['order_sn'];
            $ordersninfo[$key]['data']=$salerecord->where($where)->select();
            $ordersninfo[$key]['count']=$salerecord->where($where)->count();
            $where['static'] = 1;
            $ordersninfo[$key]['sucesscount']=$salerecord->where($where)->count();
            unset($where);
            $ordersninfo[$key]['falsecount'] = $ordersninfo[$key]['count'] - $ordersninfo[$key]['sucesscount'];
            $ordersninfo[$key]['moneycount'] = $ordersninfo[$key]['sucesscount'] * $ordersninfo[$key]['data'][0]['saleprice'];
            $cardname = $card->where('id='.$ordersninfo[$key]['data'][0]['card_id'])->getField('name');
            foreach( $ordersninfo[$key]['data'] as  $k => $v) {
                $ordersninfo[$key]['data'][$k]['name']= $cardname;
            }
        }
        $this->assign('ordersninfo',$ordersninfo);
        $this->display();
    }

    public function ajaxCardInfo(){        
        $typeid = I('typeid',110,'intval');
        $card = M('Card');
        $where['type_id']= $typeid;
        $where['offline']= array('neq',1);
        $cardinfo = $card->where($where)->select();
        $this->cardinfo = $cardinfo;
        $this->display('ajaxCardInfo');
    }

    public function ajaxCardPrices(){
        $id = I('id', 0, 'intval');
        $where['card_id'] = $id;
        $result_list = M('CardPrice')->where($where)->select();
        $card = M('Card');
        $wheredata['id']= $id;
        $sale_proportion = $card->where($wheredata)->getField('sale_proportion');
        if($result_list == ''){
            $this->ajaxReturn(V(0,'数据错误'));
        }
        $result_list = json_encode($result_list);
        $this->ajaxReturn(V(1,$sale_proportion,$result_list));
    }


}
