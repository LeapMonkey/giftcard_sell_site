<?php
namespace Sqnbcadmin\Model;
use Think\Model;

/**
 * 后台招聘管理
 * @author yangchunfu QQ:779733435 liniukeji.com
 *
 */
class RecruitImageTextModel extends Model {
    protected $insertFields = array('name', 'ename','photo_path','m_photo_path', 'description', 'edescription','add_time', 'disable', 'sort');
    protected $updateFields = array('id', 'name', 'ename','photo_path', 'm_photo_path', 'description', 'edescription','add_time', 'disable', 'sort');

    protected $selectFields = array('id', 'name', 'photo_path', 'm_photo_path','add_time', 'disable', 'sort');
    protected $detailFields = array('id', 'name', 'ename','photo_path', 'm_photo_path','description', 'edescription','add_time', 'disable', 'sort');

    protected $_validate = array(
    	array('name', 'require', '中文职务名称不能为空', self::MUST_VALIDATE, 'regex', 3),
        array('name', '1,80', '中文职务名称为1到70个字符', self::MUST_VALIDATE, 'length', 3),
        
        array('ename', 'require', '英文职务名称不能为空', self::MUST_VALIDATE, 'regex', 3),
        array('ename', '1,200', '英文职务名称为1到70个字符', self::MUST_VALIDATE, 'length', 3),

        array('description', '1,1000', '中文描述长度为1到80个字符', self::VALUE_VALIDATE, 'length', 3),
        array('edescription', '1,3000', '中文描述长度为1到3000个字符', self::VALUE_VALIDATE, 'length', 3),

        array('add_time', 'number', '时间必须为数字', self::VALUE_VALIDATE, 'regex', 3),

        array('photo_path', '1,255', 'PC图片上传有误', self::VALUE_VALIDATE, 'length', 3),
        array('m_photo_path', '1,255', '手机端图片上传有误', self::VALUE_VALIDATE, 'length', 3),

        array('sort', 'number', '排序必须为数字', self::VALUE_VALIDATE, 'regex', 3),
        array('sort', '0,10000', '排序范围为1到10000', self::VALUE_VALIDATE, 'between', 3),
       
        array('disable', '0,1', '状态只能为0或1', self::VALUE_VALIDATE, 'in', 3),


    );

    public function getRecruitImageTextByPage($where, $field = null, $order='sort'){
    	if($field == null){
    		$field = $this->selectFields;
    	}

    	$count = $this->where($where)->count();
    	$pageData = get_page($count);

    	$list = $this->field($field)->where($where)->limit($pageData['limit'])->order($order)->select();

    	return $list;
    }

    public function getRecruitImageTextDetailById($id ,$field = null){
    	if($field == null) $field = $this->detailFields;

    	$where['id'] = $id;
    	$info = $this->where($where)->field($field)->find();

    	return $info;
    }

    
	protected function _before_insert(&$data, $option){
		$data['add_time'] = time();
	}
}
