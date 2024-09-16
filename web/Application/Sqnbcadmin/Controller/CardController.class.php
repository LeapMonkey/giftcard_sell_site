<?php
namespace Sqnbcadmin\Controller;

/**
 * 卡片管理控制器
 * @author zhengnian
 */
class CardController extends AdminCommonController {

    public function index() {
        //针对不同卡类设置权限的
         $adminInfo=D('Admin')->getAdminGroup();
        if($adminInfo['group_id']==14){
            $where['type_id']=$adminInfo['card_type'];
        }
        $data = D("Card")->getInfo($where);
        if($data) {
            $this->assign('data',$data['data']);
            $this->assign('page',$data['page']);
        }
        $this->display();
    }

    public function edit($id = 0){
        $id = I('id', 0, 'intval');
        if(IS_POST){
            $data = I('post.', '');
            $data['add_time'] = time();
 
            $result = I('result','');
            if(D('Card')->create($data) !== false){
                if ($id > 0) {                   
                    D('Card')->where('id='. $id)->save();
                        $where['card_id'] = $id;
                        M('CardPrice')->where($where)->delete();

                } else {
                   $id = D('Card')->add();
                }
                $cardprice = M('CardPrice');
                foreach ($result as $key => $value) {
                    $price = $result[$key];
                    $price['add_time'] = time();
                    $price['card_id'] = $id;
                    $cardprice->add($price);                
                    unset($price);                
                }
                $this->ajaxReturn(V(1, '保存成功'));
            } else {
                $this->ajaxReturn(V(0, D('Card')->getError()));
            }
        } else {

            $CardType = M('CardType')->select();
            $this->assign('CardType', $CardType);
            $CardInfo = M('Card')->field(true)->find($id);
            $where['card_id'] = $id;
            $result_list = M('CardPrice')->where($where)->select();
            $this->assign('result_count', count($result_list));
            $this->assign('info', $CardInfo);
            $this->assign('result_list', $result_list);            
            $this->display();
        }
    }
    public function ajaxCardNotice(){
        $order_amount = M('SaleRecord')->where("flag=1")->count();
        $payment_amount =  M('PaymentRecord')->where("flag=1")->count();
        $realment_amount = M('User')->where('is_permission=2')->count();
        $arr['realment_amount']=$realment_amount;
        $arr['order_amount']=$order_amount;
        $arr['payment_amount'] = $payment_amount;

      $this->ajaxReturn($arr);

    }

    public function del(){
        $this->_del('Card');
    }

    public function delFile(){
        $this->_delFile();
    }

    public function uploadImg(){
        $this->_uploadImg();
    }
}
