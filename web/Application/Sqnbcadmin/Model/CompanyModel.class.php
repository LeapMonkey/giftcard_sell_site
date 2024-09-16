<?php
namespace Sqnbcadmin\Model;
use Think\Model;
/**
 * 分公司管理
 * @author zhengnian
 *
 */
class CompanyModel extends Model{
	protected $insertFields = array('name', 'province_id','city_id','country_id','address','tel','content','qq','email');
	protected $updateFields = array('id','name', 'province_id','city_id','country_id','address','tel','content','qq','email');
	protected $selectFields = array('id','name', 'province_id','city_id','country_id','address','tel','content','qq','email');

	protected $_validate = array(
		array('name', '1,20', '公司名称长度有误', self::MUST_VALIDATE, 'length', 3),
        array('name', '', '公司名称已存在', self::VALUE_VALIDATE, 'unique', 3),
		array('province_id', '1,10', '请选择省份', self::MUST_VALIDATE, 'length', 3),
		array('city_id', '1,10', '请选择城市', self::MUST_VALIDATE, 'length', 3),
		array('country_id', '1,10', '请选择县区', self::MUST_VALIDATE, 'length', 3),
        array('address', '2,100', '地址长度不符合要求', self::MUST_VALIDATE, 'length', 3),
		array('qq','require','请输入QQ号码。'),
		array('qq','number','QQ号码不正确。'),
		array('email', 'isEmail', '电子邮箱格式不正确', self::VALUE_VALIDATE, 'function', 3),
		array('tel','require','联系方式不能为空', self::MUST_VALIDATE),
		array('content','require','标题内容不能为空', self::MUST_VALIDATE),
	);

	protected  function checkTel($tel){
		if(!preg_match('/^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/', $tel)){
			return false;
		}else{
			return true;
		}
	}

	public function getInfo(){
		$name = I('name', '','trim');
		if($name) {
			$map['name'] = array('like',"%$name%");
		}
		$data = $this->getCompanyByPage($map, $field=null, $order='id');
		return $data;
	}

	public function getCompanyByPage($map, $field=null,$where,$order='id'){

		if ($field == null) {
			$field = $this->selectFields;
		}
		$count = $this->where($map)->where($where)->count();
		$page = get_page($count);
		$data = $this->field($field)->where($map)->where($where)->limit($page['limit'])->order($order)->select();
		return array(
			'list' => $data,
			'page' => $page['page']
		);

	}

	public function getCompanyNameById($id){
		$positionName = $this->where(array('id'=>$id))->getField('name');
		return $positionName;
	}

}
