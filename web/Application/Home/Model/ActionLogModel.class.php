<?php

namespace Home\Model;
use Think\Model;

/**
 * 操作日志管理
 * @author zhaojiping QQ:17620286 liniukeji.com
 *
 */
class ActionLogModel extends Model
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
    protected $selectFields = array('id', 'user_id','money','type', 'add_time', 'ip', 'message');

    public function getActionLogByPage($where, $field=null, $order='add_time desc',$type){
        if ($field == null) {
            $field = $this->selectFields;
        }
        $count =$this
            ->where($where)
            ->field($field)
            ->count();
        $page = get_page($count);
        $limit = $page['limit'];
        $list =$this
            ->where($where)
            ->field($field)
            ->limit($limit)
            ->order($order)
            ->select();
        return array(
            'list' => $list,
            'page' => $page['page']
        );
    }

}
