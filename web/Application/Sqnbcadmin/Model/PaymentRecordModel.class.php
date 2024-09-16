<?php
namespace Sqnbcadmin\Model;
use Think\Model;
/**
 * 卡片管理控制器
 * @author ddf
 */
class PaymentRecordModel extends Model{
    protected $insertFields = array('user','add_time','pay_cash');
    protected $updateFields = array('user','add_time','pay_cash');
    protected $selectFields = array('id','sale_record_id','member_name','alipaycount','balance','pay_cash','user','flag','add_time','flag_time','static','bankname');
    protected $_validate = array(
        array('user','require','用户名不能为空', self::MUST_VALIDATE),

    );
    
    public function getInfo($type,$falg_type){
        $name = I('name', '','trim');
        if($name) {
            $map['member_name'] = array('like',"%$name%");
        }
       
        if($falg_type) {
            $map['falg_type'] = array('eq',$falg_type);
        }
        if($type){
            $map['flag'] = array('eq',$type);
        }
        $data = $this->getSaleRecordByPage($map, $field=null, $order='id desc',$type);

        return $data;
    }


    protected function _before_insert(&$data,$option) {
        $data['add_time'] = time();
    }
    
    public function getSaleRecordByPage($where, $field=null, $order='id desc',$type){

        if ($field == null) {
            $field = $this->selectFields;
        }
        $count = $this->where($where)->count();
        $page = get_page($count);
        $limit = $page['limit'];
        if($type == 2){
            $order='id desc';
        }else{
            $order='id asc';
        }
        $data = $this->field($field)->where($where)->limit($limit)->order($order)->select();

        return array(
            'data' => $data,
            'page' => $page['page']
        );
    }
    public function getPaymentListByExcel($map, $field=null, $order='id desc'){
        /* $list = $this->alias('order')
             ->join('__MEMBER__ as member on member.id = login_history.member_id','left')
             ->where($where)
             ->field('login_history.id,login_history.failure_name,login_history.member_id,login_history.login_time,login_history.login_ip,login_history.status,login_history.remark')->order('id desc')->select();*/
        /*   $keywords = I('keywords', '');
           if ($keywords!='') {
               $where['order_sn'] = $keywords;
               $this->keywords = $keywords;
           }*/


        $list = $this->field('id','sale_record_id','member','alipaycount','balance','pay_cash','user','flag','add_time','flag_time','static')->where($map)->order($order)->select();

      //  $roomModel = D('HotelBranchHouse');//获取房间类型
        // $hotelBranchModel = D('HotelBranch');//获取酒店名称



        return $list;
    }


}
