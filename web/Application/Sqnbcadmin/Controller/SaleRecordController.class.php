<?php
namespace Sqnbcadmin\Controller;

/**
 * 卡片类型管理控制器
 * @author ddf
 */
class SaleRecordController extends AdminCommonController {

    public function index() {
        $flag= I('flag',1,intval);
        //搜索的已审核的合格以及不合格的状态
        $flag_type=I('flag_type');
        if($flag_type!=''){
            $where['static']=$flag_type;
        }
        //针对不同卡类设置权限的
        $adminInfo=D('Admin')->getAdminGroup();
        if($adminInfo['group_id']==14){
            $where['a.type_id']=$adminInfo['card_type'];
        }
        $data = D("SaleRecord")->getInfo($flag,$where);
//p($data);die;
        $card =M('Card');
        $cardtype = M('CardType');
        $user = M('User');
        foreach ($data['data'] as $k => $v) {
            $cardInfo = $card->where('id='.$v['card_id'])->getField('name');
            $userinfo = $user->where('id='.$v['user_id'])->find();
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

       //  $tag = I('static','','intval');
        $userid = I('userid','','intval');
        $this->assign('userid',$userid);
        $this->assign('tag',$flag);
        if($data) {
            $this->assign('data',$data['data']);
            $this->assign('page',$data['page']);
        }

        $this->display();
    }

    //卡片审核
    public function cardRecommend(){
        $id = I('id', 0, 'intval');
        $saleRecord = D('SaleRecord');
        $salerecordinfo = $saleRecord->where('id='.$id)->find();
        if($salerecordinfo['flag'] == 2){
            $this->error('已经审核');
             exit;
        }
        $data['flag'] = '2';
        $data['static'] = '1';
        $data['flag_time'] = time();
        $result = $saleRecord->where('id='.$id)->save($data);
        $salerecord = D("SaleRecord");
//        if($salerecordinfo['card_price_id'] == 'zdy'){
//            $cardinfo = M('Card')->where('id='.$salerecordinfo['card_id'])->find();
//            $money=$salerecordinfo['price']*$cardinfo['sale_proportion'];
//        }else{
//            $cardpriceinfo= M('CardPrice')->where('id='.$salerecordinfo['card_price_id'])->find();
//
//            $money = $cardpriceinfo['sale_price'];
//
//        }
//        if($salerecordinfo['express'] == 1){
//            $money = $money * 0.97;
//        }

        $balance = M('User')->where('id='.$salerecordinfo['user_id'])->getField('balance');
        $money = $balance +$salerecordinfo['actual_price'];

        unset($data);
        $data['balance']=$money;
        $balanceresult = M('User')->where('id='.$salerecordinfo['user_id'])->save($data);

        if ($result!==false && $balanceresult!==false){
        	$order_save['end_time'] = time();
        	$where_order['order_id']=$salerecordinfo['order_id'];
            $where_order['flag']=2;
            $where_dai['flag']=array("eq",'1');
            $where_yes['static']=array("eq",'1');
            $where_no['static']=array("in",'0,2');
        	$count=$saleRecord->where($where_order)->where($where_dai)->count();
        	$count_yes=$saleRecord->where($where_order)->where($where_yes)->count();
        	$count_no=$saleRecord->where($where_order)->where($where_no)->count();
            $order_amount =  M('Order')->where('order_id='.$salerecordinfo['order_id'])->getField('order_amount');
          //p($count);p($count_yes);p($count_no);die;
        	if($count==0 && $count_yes>0){
        			$order_save['order_status']=2;

        	}elseif($count==0 && $count_yes==0 && $count_no>0){
					$order_save['order_status']=3;
        	}
            $order_save['order_amount']=$salerecordinfo['actual_price'] + $order_amount;
        	$orders= M('Order')->where('order_id='.$salerecordinfo['order_id'])->save($order_save);
        	//p($orders['order_status']);p($orders['order_amount']);die;
        	$orderInfo=M('Order')->where('order_id='.$salerecordinfo['order_id'])->find();
            if(($orderInfo['order_status']==2 || $orderInfo['order_status']==3) && $orderInfo['order_amount']>0){
                //添加操作日志
                $actionLog=M('ActionLog');
                $log['user_id']=$orderInfo['user_id'];
                $log['add_time']=time();
                $log['type']=2;
                $log['ip']=get_client_ip();
                $log['money']=$order_save['order_amount'];
                $log['message']="销售卡卷获得余额：".$order_save['order_amount']."(自动结算)";
                $actionLog->add($log);
            }
            $this->success('审核成功!',U('SaleRecord/index'));
            exit;
        }else{

            $this->error('审核失败');
        }
    }
   //审核不通过
    public function cardNoRecommend(){
        $id = I('id', 0, 'intval');
        $saleRecord =M('SaleRecord');
         $salerecordinfo = $saleRecord->where('id='.$id)->find();
        if($salerecordinfo['flag'] == 2){
            $this->error('已经审核');
             exit;
        }
        $data['flag'] = '2';
        $data['static'] = '0';
        $data['flag_time'] = time();
        $data['remarks']=I('remarks');
        $result = $saleRecord->where('id='.$id)->save($data);

        if ($result!==false){
            $result_order=$this->editOrderStatus($salerecordinfo['order_id']);
            if($result_order!==false){
                $this->success('审核成功!',U('SaleRecord/index'));
                exit;
            }else{
                $this->error('审核失败');
                exit;
            }


        }else{
            $this->error('审核失败');
             exit;
        }

    }
    //修改订单状态
    public function editOrderStatus($order_id){
        $saleRecord =M('SaleRecord');
        $order_save['end_time'] = time();
        $where_order['order_id']=$order_id;
        $where_order['flag']=2;
        $where_dai['flag']=array("eq",'1');
        $where_yes['static']=array("eq",'1');
        $where_no['static']=array("in",'0,2');
        $count=$saleRecord->where($where_order)->where($where_dai)->count();
        $count_yes=$saleRecord->where($where_order)->where($where_yes)->count();
        $count_no=$saleRecord->where($where_order)->where($where_no)->count();
        // p($count);p($count_yes);p($count_no);die;
        if($count==0 && $count_yes>0){
            $order_save['order_status']=2;
        }elseif($count==0 && $count_yes==0 && $count_no>0){
            $order_save['order_status']=3;
        }
        $orders= M('Order')->where('order_id='.$order_id)->save($order_save);
        return $orders;
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

    public function del(){//批量不通过
        //$this->_del('CardType');
        $id = I('id', 0);
        $result = V(0, '审核失败, 未知错误');
        $saleModel=M("SaleRecord");
        if($id != 0){        
            $where['id'] = array('in', $id);
            $data['flag'] = '2';
            $data['static'] = '0';
            $data['flag_time'] = time();
            if( $saleModel->data($data)->where($where)->save() !== false){
                //$result = V(1, '审核成功');
                $orders=$saleModel->where($where)->group('order_id')->select();
                foreach($orders as $key=>$v){
                    $this->editOrderStatus($v['order_id']);
                }
                $result = V(1, '审核成功');
            }
        }
        $this->ajaxReturn($result);
    }


    public function delFile(){
        $this->_delFile();
    }

    public function uploadImg(){
        $this->_uploadImg();
    }


    //导出
    public function exportExcel(){
        $tag = I('tag','','intval');
        if($tag != ''){
            $condition['flag'] = $tag;
        }
         /*$flag= I('flag',1,intval);
        $data = D("SaleRecord")->getInfo($flag);
        $card =M('Card');
        $cardtype = M('CardType');
        $user = M('User');
        foreach ($data['data'] as $k => $v) {
            $cardInfo = $card->where('id='.$v['card_id'])->getField('name');
            $userinfo = $user->where('id='.$v['user_id'])->find();
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

        }*/

        /*$timegap = I('timegap');
        if($timegap){
            $gap = explode('|', $timegap);
            $begin = strtotime($gap[0]);
            $end = strtotime($gap[1]);
        }
        if($begin && $end){
            $condition['order_time'] = array('between',"$begin,$end");
        }*/

        $data = D("SaleRecord")->getExcel($tag);

        if (empty($data)) {
            exit;
        }
          $card =M('Card');
        $cardtype = M('CardType');
        $user = M('User');
        foreach ($data as $k => $v) {
            $cardInfo = $card->where('id='.$v['card_id'])->getField('name');
            $userinfo = $user->where('id='.$v['user_id'])->find();
            $data[$k]['username'] = $userinfo['username'];
            $data[$k]['realname'] = $userinfo['name'];
            $data[$k]['name'] = $cardInfo ;
            if( $data[$k]['flag'] == 1){
                $data[$k]['flagstatic'] = "未审核";
            }else{
                if($data[$k]['static'] == 1){
                    $data[$k]['flagstatic'] = "合格";
                }else{
                    $data[$k]['flagstatic'] = "不合格";
                }
            }
            //unset($data[$key]['member']);

        }

        foreach ($data as $key => $value) {



            $dataa[$key][0] = $data[$key]['id'];
            $dataa[$key][1] = $data[$key]['username'];
            $dataa[$key][2] = $data[$key]['name'];
            $dataa[$key][3] = $data[$key]['price'];
            $dataa[$key][4] = $data[$key]['cardkey'].' ';
            $dataa[$key][5] = $data[$key]['password'].' ';
            $dataa[$key][6] = $data[$key]['flagstatic'];
            $dataa[$key][7] =date('Y-m-d H:i:s',$data[$key]['add_time']); 
      

         
        }
       // p( $data);die;
        //p($data);die;
        $title_array = array('id','用户名','卡片名称','卡片金额','卡片账号','卡片密码','卡片状态','提交时间');
        array_unshift($dataa, $title_array);
        $count = count($dataa);
        create_xls($dataa, '用户售卡记录表', '用户售卡记录表', '用户售卡记录表',array('A','B','C','D','E','F','G','H'),$count);
    }



}
