<?php
namespace Sqnbcadmin\Model;
use Think\Model;
/**
 * 协议控制器
 * @author zhengnian
 */
class ProtocolModel extends Model{
    protected $insertFields = array('content');
    protected $updateFields = array('content');
    protected $selectFields = array('id','content');

    protected $_validate = array(
        array('content','require','内容不能为空', self::MUST_VALIDATE),
    );
    
}
