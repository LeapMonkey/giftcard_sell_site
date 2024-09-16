<?php

namespace Home\Model;

use Think\Model;

/**
 * 后台用户管理
 * @author zhaojiping QQ:17620286 liniukeji.com
 *
 */
class UserModel extends Model
{
    protected $insertFields = array('name','id_card','id_card_phone_upon','id_card_phone_down','id_card_phone_hold','is_permission');
    protected $updateFields = array('name','id_card','id_card_phone_upon','id_card_phone_down','id_card_phone_hold','is_permission');
    protected $selectFields = array('id','username','phone','password','is_member','add_time','is_permission','add_time','name','balance','disabled');

    protected $_validate = array(
        array('name','require','真实姓名不能为空', self::MUST_VALIDATE),
        array('name', '2,5', '真实姓名不正确, 请输入2到5个字', self::MUST_VALIDATE, 'length', 1),
        array('id_card','require','身份证不能为空', self::MUST_VALIDATE),
        array('id_card_phone_upon','require','身份证国徽面不能为空', self::MUST_VALIDATE),
        array('id_card_phone_down','require','身份证人像面不能为空', self::MUST_VALIDATE),
        array('id_card_phone_hold','require','手持身份证照不能为空', self::MUST_VALIDATE),
    );
    //上线的时候再打开,调用接口的



}
