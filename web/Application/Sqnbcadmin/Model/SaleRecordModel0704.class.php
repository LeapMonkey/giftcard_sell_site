<?php
namespace Sqnbcadmin\Model;
use Think\Model;
/**
 * 卡片管理控制器
 * @author ddf
 */
class SaleRecordModel extends Model{
    protected $insertFields = array('name','add_time','photo_path');
    protected $updateFields = array('name','add_time','photo_path');
    protected $selectFields = array('id','card_id','card_price_id','price','submission_type','cardkey','password','user_id','flag','add_time','static','express','static','imgs','actual_price');
    protected $_validate = array(
        array('price','require','卡片价格不能为空', self::MUST_VALIDATE),

    );
    
    public function getInfo($type,$map){
        $name = I('name', '','trim');
        if($name) {
            $salemap['name'] = array('like',"%$name%");
            $cardid = M('Card')->where($salemap)->getField('id',true);
            $map['a.card_id'] = array('in',$cardid);
        }
        $userid = I('userid', '','trim');
        if($userid) {
       
            $map['a.user_id'] = $userid;
        }
        
        if($type){
            $map['flag'] = array('eq',$type);
        }
        if($type == 2){
           $order='id desc'; 
        }else{
            $order='id asc';
        }

        $data = $this->getSaleRecordByPage($map, $field=null, $order,$type);
        return $data;
    }

    protected function _before_insert(&$data,$option) {
        $data['add_time'] = time();
    }
    
    public function getSaleRecordByPage($where, $field=null, $order='',$type){
        $field="a.id,a.card_id,a.price,a.saleprice,a.cardkey,a.password,a.flag,a.express,a.user_id,o.add_time,o.order_sn,a.static,a.imgs,a.actual_price";
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
        $data = $this->alias('a')
            ->field($field)
            ->join('__ORDER__ o on o.order_id=a.order_id','left')
            ->where($where)
            ->limit($limit)
            ->order($order)
            ->select();
        return array(
            'data' => $data,
            'page' => $page['page']
        );
    }
    public function getExcel($type){
        $name = I('name', '','trim');
        if($name) {
            $map['name'] = array('like',"%$name%");
        }
        if($type){
            $map['flag'] = array('eq',$type);
        }
        $data = $this->getSaleRecordByExcel($map, $field=null, $order='id desc',$type);
        return $data;
    }

    public function getSaleRecordByExcel($where, $field=null, $order='id desc'){

        if ($field == null) {
            $field = $this->selectFields;
        }
      

        $data = $this->field($field)->where($where)->order($order)->select();

        return  $data;
    }


}
