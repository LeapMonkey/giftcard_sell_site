<?php
namespace Sqnbcadmin\Model;
use Think\Model;
use Common\Tools\Emchat;
/**
 * 后台用户管理
 * @author ddf QQ:550308208 liniukeji.com
 *
 */
class AreaModel extends Model{
	protected $insertFields = array('name','level','parent_id');
	protected $updateFields = array('name','level','parent_id');
	protected $selectFields = array('id','name','level','parent_id');
	protected $_validate = array(
		array('name','require','地区名称内容不能为空', self::MUST_VALIDATE),
        array('name', '1,30', '地区名称不能超过30', self::MUST_VALIDATE, 'length', 3),		
	);
			
	// 获取地区信息
	public function getNameById($id){
		$condition['id'] = $id;
		$areaname = $this->where($condition)->getField('name');
		return $areaname;
	}	
}
