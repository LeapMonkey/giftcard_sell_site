<?php
namespace Sqnbcadmin\Model;
use Think\Model;
/**
 * 后台角色
 * @author liuyang 594353482
 *
 */
class RoleModel extends Model{
	protected $insertFields = array('name','remark','sort','status');
	protected $updateFields = array('id','name','remark','sort','status');	

	protected $_validate = array(
		array('name', '1,30', '角色名称长度有误', self::MUST_VALIDATE, 'length', 3),
		array('sort', 'number', '排序字段只能是一个数字'),
		array('range', 'number', '范围只能是一个数字'),
		array('remark', '0,1000', '角色描述过长', self::MUST_VALIDATE, 'length', 3),
	);

	protected function _after_insert(&$data,$option){
		$name = $data['name'];
		$pinyin = new \Common\Tools\ChineseToPinyin();
		$info['pinyin'] = $pinyin->getAllPinyi($name);
		$this->where(array('id'=>$data['id']))->save($info);
	}

	public function getRoleNameById($id){
		$roleName = $this->where(array('id'=>$id))->getField('name');
		return $roleName;
	}

	public function getAllRoleList(){
        $roleList = $this->where(array('status'=>0))->order('sort')->field('id, name')->select();
        return $roleList;
	}

}
