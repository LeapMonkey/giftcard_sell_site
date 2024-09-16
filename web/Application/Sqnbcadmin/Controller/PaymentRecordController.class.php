<?php
namespace Sqnbcadmin\Controller;

/**
 * 卡片类型管理控制器
 * @author ddf
 */
class PaymentRecordController extends AdminCommonController {

    public function index() {

        $flag = I('flag',2);
       $data = D("PaymentRecord")->getInfo($flag);

        $card =M('Card');
        $cardtype = M('CardType');
        $user = M('User');
        foreach ($data['data'] as $k => $v) {
            $cardInfo = $card->where('id='.$v['card_id'])->getField('name');

            $userinfo = $user->where('id='.$v['user'])->find();

            $data['data'][$k]['username'] = $userinfo['username'];
            $data['data'][$k]['realname'] = $userinfo['name'];

            $data['data'][$k]['name'] = $cardInfo ;
        }

        $tag = I('static','','intval');

        $this->assign('tag', $flag);

        if($data) {
            $this->assign('data',$data['data']);
            $this->assign('page',$data['page']);
        }

        $this->display();
    }

    public function aliapply() {

        $flag = I('flag',1);
        $falg_type = 1;
        $data = D("PaymentRecord")->getInfo($flag,$falg_type);

        $card =M('Card');
        $cardtype = M('CardType');
        $user = M('User');
        foreach ($data['data'] as $k => $v) {
            $cardInfo = $card->where('id='.$v['card_id'])->getField('name');

            $userinfo = $user->where('id='.$v['user'])->find();

            $data['data'][$k]['username'] = $userinfo['username'];
            $data['data'][$k]['realname'] = $userinfo['name'];

            $data['data'][$k]['name'] = $cardInfo ;
            if( $data['data'][$k]['flag'] == 1){
                $data['data'][$k]['flagstatic'] = "未审核";
            }else{
                if($data['data'][$k]['static'] == 1){
                    $data['data'][$k]['flagstatic'] = "合格";
                }else{
                    $data['data'][$k]['flagstatic'] = "不合格";
                }
            }

        }
        $tag = I('static','','intval');

        $this->assign('tag', 1);

        if($data) {
            $this->assign('data',$data['data']);
            $this->assign('page',$data['page']);
        }

        $this->display();
    }
    public function bankapply() {

        $flag = I('flag',1);
        $falg_type = 2;
        $data = D("PaymentRecord")->getInfo($flag,$falg_type);

        $card =M('Card');
        $cardtype = M('CardType');
        $user = M('User');
        foreach ($data['data'] as $k => $v) {
            $cardInfo = $card->where('id='.$v['card_id'])->getField('name');

            $userinfo = $user->where('id='.$v['user'])->find();

            $data['data'][$k]['username'] = $userinfo['username'];
            $data['data'][$k]['realname'] = $userinfo['name'];
           // $data['data'][$k]['bankname'] = $userinfo['bankname'];
            $data['data'][$k]['bankphone'] = $userinfo['bankphone'];

            $data['data'][$k]['name'] = $cardInfo ;
            if( $data['data'][$k]['flag'] == 1){
                $data['data'][$k]['flagstatic'] = "未审核";
            }else{
                if($data['data'][$k]['static'] == 1){
                    $data['data'][$k]['flagstatic'] = "合格";
                }else{
                    $data['data'][$k]['flagstatic'] = "不合格";
                }
            }

        }

        $tag = I('static','','intval');

        $this->assign('tag', 2);

        if($data) {
            $this->assign('data',$data['data']);
            $this->assign('page',$data['page']);
        }

        $this->display();
    }
    public function alipay($out_biz_no,$amount,$payee_account,$payee_real_name){
      
        //Vendor("AlipaySdk.AopSdk");
        Vendor("AlipaySdk.aop.AopClient");
        Vendor("AlipaySdk.aop.AlipayFundTransToaccountTransferRequest");
        Vendor("AlipaySdk.aop.SignData");
 
        $aop = new \AopClient ();
        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = C('ALIPAYAPPID');

        $aop->rsaPrivateKey =  C('RSA_PRIVATE_KEY');
        $aop->alipayrsaPublicKey= C('ALIPAYRSAPUBLICKEY');
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset='UTF-8';
        $aop->format='json';
       // $out_biz_no = '07151350188126';
      //  $amount = '0.1';
      //  $payee_account = '84901801@qq.com';
      //  $payee_real_name = '杜登峰';
        $request = new \AlipayFundTransToaccountTransferRequest ();

            $request->setBizContent("{" .
        "\"out_biz_no\":".$out_biz_no."," .
        "\"payee_type\":\"ALIPAY_LOGONID\"," .
        "\"payee_account\":\"".$payee_account."\"," .
        "\"amount\":\"".$amount."\"," .
        "\"payer_show_name\":\"提现\"," .
        "\"payee_real_name\":\"".$payee_real_name."\"," .
        "\"remark\":\"备注\"" .
        "  }");
        $result = $aop->execute ( $request); 
        $aa= $request->getApiMethodName();

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";

   
        $resultCode = $result->$responseNode->code;

        if(!empty($resultCode)&&$resultCode == 10000){
        return 1;
        } else {
        return $resultCode;
        }

    }


    //卡片审核
    public function payRecommend(){
        $id = I('id', 0, 'intval');
        $SaleRecord = D('PaymentRecord');
        $data['flag'] = '2';
        $data['static'] = '2';
        $data['flag_time'] = time();
        

        $salerecordinfo = $SaleRecord->where('id='.$id)->find();
        $where['id'] = $salerecordinfo['user'];
        $balance = M('User')->where($where)->getField('balance');
        $money = $balance - $salerecordinfo['pay_cash'];

//        if($balance < $salerecordinfo['pay_cash']){
//            $this->error('余额不足');
//        }

//        $alipay= $this->alipay($salerecordinfo['sale_record_id'],$salerecordinfo['pay_cash'],$salerecordinfo['alipaycount'],$salerecordinfo['member_name']);
//        if($alipay != 1){
//
//            if ($alipay == '40004') {
//                $this->error('提现失败,帐号姓名不匹配');
//            }
//            $this->error('提现失败,请检查企业支付宝账户,错误代码'.$alipay);
//        }
        $result = $SaleRecord->where('id='.$id)->save($data);
        unset($data);
        
//        $data['balance']=$money;
//        $balanceresult = M('User')->where('id='.$salerecordinfo['user'])->save($data);


        if ($result){
            //添加操作日志
            $actionLog=M('ActionLog');
            $log['user_id']=$salerecordinfo['user'];
            $log['add_time']=time();
            $log['type']=3;
            $log['ip']=get_client_ip();
            $log['money']=$salerecordinfo['pay_cash'];
            // $log['message']="销售卡卷获得余额：".$order_amount."(自动结算)";
            $actionLog->add($log);
            $this->success('审核成功!',U('PaymentRecord/index'));
            exit;
        }else{

            $this->error('审核失败');
        }

    }
//银行打款卡片审核
    public function bankpayRecommend(){
        $id = I('id', 0, 'intval');
        $SaleRecord = D('PaymentRecord');
        $data['flag'] = '2';
        $data['static'] = '2';
        $data['flag_time'] = time();
        

        $salerecordinfo = $SaleRecord->where('id='.$id)->find();
        $where['id'] = $salerecordinfo['user'];
        $balance = M('User')->where($where)->getField('balance');
        $money = $balance - $salerecordinfo['pay_cash'];
   
//        if($balance < $salerecordinfo['pay_cash']){
//            $this->error('余额不足');
//        }

        // $alipay= $this->alipay($salerecordinfo['sale_record_id'],$salerecordinfo['pay_cash'],$salerecordinfo['alipaycount'],$salerecordinfo['member_name']);
        // if($alipay != 1){
        //     if ($alipay == '40004') {
        //         $this->error('提现失败,帐号姓名不匹配');
        //     }
        //     $this->error('提现失败,请检查企业支付宝账户,错误代码'.$alipay);
        // }
        $result = $SaleRecord->where('id='.$id)->save($data);
        unset($data);
        
//        $data['balance']=$money;
//        $balanceresult = M('User')->where('id='.$salerecordinfo['user'])->save($data);


        if ($result ){
            //添加操作日志
            $actionLog=M('ActionLog');
            $log['user_id']=$salerecordinfo['user'];
            $log['add_time']=time();
            $log['type']=3;
            $log['ip']=get_client_ip();
            $log['money']=$salerecordinfo['pay_cash'];
           // $log['message']="销售卡卷获得余额：".$order_amount."(自动结算)";
            $actionLog->add($log);
            //p($actionLog->_sql());die;
            $this->success('审核成功!',U('PaymentRecord/index'));
            exit;
        }else{

            $this->error('审核失败');
        }

    }
    public function payNoRecommend(){
        $id = I('id', 0, 'intval');
        $SaleRecord = D('PaymentRecord');
        $data['flag'] = '2';
        $data['static'] = '1';
        $data['flag_time'] = time();
        $data['remarks']=I('remarks');
        $result = $SaleRecord->where('id='.$id)->save($data);

        $salerecordinfo = $SaleRecord->where('id='.$id)->find();
        $where['id'] = $salerecordinfo['user'];
        $balance = M('User')->where($where)->getField('balance');
        $money = $balance + $salerecordinfo['pay_cash'];


//        if($balance < $salerecordinfo['pay_cash']){
//            $this->error('余额不足');
//        }
        $data['balance']=$money;
        $balanceresult = M('User')->where('id='.$salerecordinfo['user'])->save($data);
        if ($result && $balanceresult){
            if($salerecordinfo['falg_type']==1){
                $this->success('审核成功!',U('PaymentRecord/aliapply'));
            }else{
                $this->success('审核成功!',U('PaymentRecord/bankapply'));
            }

            exit;
        }else{

            $this->error('审核失败');
        }

    }
    public function edit($id = 0){
        $id = I('id', 0, 'intval');
        if(IS_POST){
            $data = I('post.', '');
            $data['add_time'] = time();
            if(D('CardType')->create($data) !== false){
                if ($id > 0) {                   
                    D('CardType')->where('id='. $id)->save();
                } else {
                    D('CardType')->add();
                }
                $this->ajaxReturn(V(1, '保存成功'));
            } else {
                $this->ajaxReturn(V(0, D('CardType')->getError()));
            }
        } else {
            $userId = UID;
            if($userId == 3){
                $where['id'] = array('eq','110');
            }
            $EductionTypeInfo = M('CardType')->where($where)->field(true)->find($id);
            $this->assign('info', $EductionTypeInfo);
            $this->display();
        }
    }

    public function exportExcel(){
        //$tag = I('tag','','intval');
//        if($tag==1){
//            $condition['static'] = 2;
//            $condition['flag'] = 2;
//        }
        $condition['static'] = 2;
        $condition['flag'] = 2;
        $data = D('PaymentRecord')->getPaymentListByExcel($condition);

        if (empty($data)) {
            exit;
        }

        foreach ($data as $key => $value) {
            $data[$key]['add_time'] = date('Y-m-d H:i:s',$data[$key]['add_time']); 
            $data[$key]['flag_time'] = date('Y-m-d H:i:s',$data[$key]['flag_time']); 
            $data[$key]['flag'] = $data[$key]['flag']==1?'未审核':'已审核';
            $data[$key]['static'] =$data[$key]['static']==2?'已打款':'未打款';
            unset($data[$key]['member']);
            unset($data[$key]['falg_type']);
            unset($data[$key]['account_id']);
            unset($data[$key]['bankname']);
            unset($data[$key]['remarks']);
        }

        $title_array = array('订单编号','姓名','提现账号','账户余额','提现金额','提现人账号id','审核状态','申请时间','审核时间','打款状态');
        array_unshift($data, $title_array);
        $count = count($data);
        create_xls($data, '提现申请记录表', '提现申请记录表', '提现申请记录表',array('A','B','C','D','E','F','G','H','I','J'),$count);
    }
//支付宝提现导出
    public function aliexportExcel(){
        $tag = I('tag','','intval');
//        if($tag==1){
//            $condition['static'] = 2;
//            $condition['flag'] = 2;
//        }
        /*$timegap = I('timegap');
        if($timegap){
            $gap = explode('|', $timegap);
            $begin = strtotime($gap[0]);
            $end = strtotime($gap[1]);
        }
        if($begin && $end){
            $condition['order_time'] = array('between',"$begin,$end");
        }*/
        $condition['falg_type'] = 1;
        $data = D('PaymentRecord')->getPaymentListByExcel($condition);

        if (empty($data)) {
            exit;
        }
        foreach ($data as $key => $value) {
            $data[$key]['add_time'] = date('Y-m-d H:i:s',$data[$key]['add_time']);
            if($data[$key]['flag_time'] !=''){
                $data[$key]['flag_time'] = date('Y-m-d H:i:s',$data[$key]['flag_time']);
            }else{
                $data[$key]['flag_time']='';
            }
           // $data[$key]['flag_time'] = date('Y-m-d H:i:s',$data[$key]['flag_time']);
            $data[$key]['flag'] = $data[$key]['flag']==1?'未审核':'已审核';
            $data[$key]['static'] =$data[$key]['static']==2?'已打款':'未打款';
            unset($data[$key]['member']);
            unset($data[$key]['falg_type']);
            unset($data[$key]['account_id']);
        }
        $title_array = array('订单编号','姓名','提现账号','账户余额','提现金额','提现人账号id','审核状态','申请时间','审核时间','打款状态');
        array_unshift($data, $title_array);
        $count = count($data);
        create_xls($data, '提现申请记录表', '提现申请记录表', '提现申请记录表',array('A','B','C','D','E','F','G','H','I','J'),$count);
    }
//银行卡提现导出
    public function bankexportExcel(){
        $tag = I('tag','','intval');

//        if($tag==1){
//            $condition['static'] = 2;
//
//        }
//        $condition['flag'] = 1;
        $condition['falg_type'] = 2;
        /*$timegap = I('timegap');
        if($timegap){
            $gap = explode('|', $timegap);
            $begin = strtotime($gap[0]);
            $end = strtotime($gap[1]);
        }
        if($begin && $end){
            $condition['order_time'] = array('between',"$begin,$end");
        }*/
        $data = D('PaymentRecord')->getPaymentListByExcel($condition);
       // p(D('PaymentRecord')->_sql());die;
        if (empty($data)) {
            exit;
        }
        foreach ($data as $key => $value) {
            $data[$key]['add_time'] = date('Y-m-d H:i:s',$data[$key]['add_time']);
            if($data[$key]['flag_time'] !=''){
                $data[$key]['flag_time'] = date('Y-m-d H:i:s',$data[$key]['flag_time']);
            }else{
                $data[$key]['flag_time']='';
            }

            $data[$key]['flag'] = $data[$key]['flag']==1?'未审核':'已审核';
            $data[$key]['static'] =$data[$key]['static']==2?'已打款':'未打款';
            unset($data[$key]['member']);
            unset($data[$key]['falg_type']);
            unset($data[$key]['account_id']);
        }
        $title_array = array('订单编号','姓名','提现账号','账户余额','提现金额','提现人账号id','审核状态','申请时间','审核时间','打款状态','开户行');
        array_unshift($data, $title_array);
        $count = count($data);
        create_xls($data, '提现申请记录表', '提现申请记录表', '提现申请记录表',array('A','B','C','D','E','F','G','H','I','J','K'),$count);
    }
    public function del(){
        $this->_del('CardType');
    }

    public function delFile(){
        $this->_delFile();
    }

    public function uploadImg(){
        $this->_uploadImg();
    }
}
