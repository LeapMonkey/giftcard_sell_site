<?php

namespace Home\Model;
use Think\Model;

/**
 * 后台用户管理
 * @author zhaojiping QQ:17620286 liniukeji.com
 *
 */
class SaleRecordModel extends Model
{

//    protected $_validate = array(
//        array('username', '4,20', '用户名不正确, 请输入4到20位字符', self::MUST_VALIDATE, 'length', 4),
//        array('password', '6,46', '登录密码长度不合法', self::MUST_VALIDATE, 'length', 4), //密码长度不合法, 只注册时验证
//        array('phone', 'require', '注册手机号码不能为空', 1),
//        array('phone', 'isMobile', '不是有效的手机号码', 1, 'function'), // 自定义函数验证密码格式
//        array('phone', '', '手机账号已经存在！', 1, 'unique', 3),
//        array('password', 'require', '密码不能为空！', 1),
//        array('password', '6,20', '密码必须是6-20位的字符！', 1, 'length'),
//    );

    public function getArticleByPage($where, $field=null, $order='type_id asc , add_time desc',$type){
        if ($field == null) {
            $field = $this->selectFields;
        }
        $userId = UID;
        $count =$this->alias('a')
            ->where($where)
            ->distinct(true)
            ->join('__CARD__ c on c.id=card_id','left')
            ->field('order_sn,a.add_time,price,saleprice,flag,a.id,static,card_id,c.name')
            ->count();
        $page = get_page($count);
        $limit = $page['limit'];
        $list =$this->alias('a')
            ->where($where)
            ->distinct(true)
            ->join('__CARD__ c on c.id=card_id','left')
            ->field('order_sn,a.add_time,price,saleprice,flag,a.id,static,card_id,c.name')
            ->limit($limit)
            ->order($order)
            ->select();
        return array(
            'list' => $list,
            'page' => $page['page']
        );
    }

    public function getCardList($where, $field=null, $order='type_id asc , add_time desc'){
        if ($field == null) {
            $field = $this->selectFields;
        }
        $cardList=$this->alias('a')
            ->where($where)
            ->group("card_id")
            ->order('id desc')
            ->join('__CARD__ c on c.id=a.card_id','left')
            ->field($field)
            ->select();
        //  p($salerecord->_sql());die;
        foreach($cardList as $key=>$v){
            if($v['card_logo']){
                $cardList[$key]['card_logo']=MOBILE_URL.$v['card_logo'];
            }
        }
        return $cardList;
    }
}
