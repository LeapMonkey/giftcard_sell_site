<?php
namespace Sqnbcadmin\Model;
use Think\Model;

/**
 * 后台招聘管理
 * @author yangchunfu QQ:779733435 liniukeji.com
 *
 */
class RecruitModel extends Model {
    protected $insertFields = array('name', 'ename','category_id', 'address', 'eaddress', 'description', 'edescription', 'add_time', 'sort', 'email','status', 'phone','disable','salary');
    protected $updateFields = array('id', 'name', 'ename','category_id', 'address', 'eaddress', 'description', 'edescription','add_time', 'sort', 'email','status', 'phone','disable','salary');

    protected $selectFields = array('id', 'name', 'category_id', 'address', 'add_time','salary', 'sort', 'status', 'email', 'phone' ,'salary','disable');
    protected $detailFields = array('id', 'name', 'ename','category_id', 'address', 'eaddress', 'description', 'edescription','add_time', 'sort', 'email','status', 'phone','disable','salary');

    protected $_validate = array(
    	array('name', 'require', '中文职务名称不能为空', self::MUST_VALIDATE, 'regex', 3),
        array('name', '1,70', '中文职务名称为1到70个字符', self::MUST_VALIDATE, 'length', 3),

        array('ename', 'require', '英文职务名称不能为空', self::MUST_VALIDATE, 'regex', 3),
        array('ename', '1,200', '英文职务名称为1到200个字符', self::MUST_VALIDATE, 'length', 3),

        array('category_id', 'number', '请选择岗位类别', self::MUST_VALIDATE, 'regex', 3),
        
        array('address', '1,80', '地址长度为1到80个字符', self::MUST_VALIDATE, 'length', 3),
        array('eaddress', '1,200', '地址长度为1到200个字符', self::MUST_VALIDATE, 'length', 3),

        array('salary', '1,80', '薪资为1到80个字符', self::MUST_VALIDATE, 'length', 3),

       /* array('email', 'isEmail', '邮箱地址不合法', self::MUST_VALIDATE, 'function', 3), 
        array('phone', 'isMobile', '手机格式不正确', self::MUST_VALIDATE, 'function', 3),*/ 


        /*array('description', '1,1000', '描述长度为1到1000个字符', self::VALUE_VALIDATE, 'length', 3),
        array('edescription', '1,2000', '描述长度为1到2000个字符', self::VALUE_VALIDATE, 'length', 3),*/
        array('add_time', 'number', '时间必须为数字', self::VALUE_VALIDATE, 'regex', 3),

        array('sort', 'number', '排序必须为数字', self::VALUE_VALIDATE, 'regex', 3),
        array('sort', '0,10000', '排序范围为1到10000', self::VALUE_VALIDATE, 'between', 3),
       
        array('status', '0,1', '状态只能为0或1', self::VALUE_VALIDATE, 'in', 3),
        array('disable', '0,1', '状态只能为0或1', self::VALUE_VALIDATE, 'in', 3),


    );

    public function getRecruitByPage($where, $field = null, $order='sort desc'){
    	if($field == null){
    		$field = $this->selectFields;
    	}

    	$count = $this->where($where)->count();
    	$pageData = get_page($count);

    	$list = $this->field($field)->where($where)->limit($pageData['limit'])->order($order)->select();

        foreach ($list as $k => $v) {
            $nameList = M('JobCategory')->where('id='.$v['category_id'])->field('name,');
            $list[$k]['category_name'] = M('JobCategory')->where('id='.$v['category_id'])->getField('name');
        }

    	return $list;
    }

    public function getRecruitDetailById($id ,$field = null){
    	if($field == null) $field = $this->detailFields;

    	$where['id'] = $id;
    	$info = $this->where($where)->field($field)->find();

    	return $info;
    }

    protected function _before_insert(&$data, $option){
        $data['add_time'] = time();
    }
}
