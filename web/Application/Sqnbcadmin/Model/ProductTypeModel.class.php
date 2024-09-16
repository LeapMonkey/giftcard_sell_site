<?php
namespace Sqnbcadmin\Model;
use Think\Model;
/**
 * 产品类型管理控制器
 * @author zhengnian
 */
class ProductTypeModel extends Model{
    protected $insertFields = array('name','add_time');
    protected $updateFields = array('name','add_time');
    protected $selectFields = array('id','name','add_time');
    protected $_validate = array(
        array('name','require','产品分类名称不能为空', self::MUST_VALIDATE),
        array('name', '1,10', '产品分类名称不能超过10', self::MUST_VALIDATE, 'length', 3),

    );
    
    public function getInfo($type){
        $name = I('name', '','trim');
        if($name) {
            $map['name'] = array('like',"%$name%");
        }
        $data = $this->getProductTypeByPage($map, $field=null, $order='add_time desc',$type);
        return $data;
    }

    protected function _before_insert(&$data,$option) {
        $data['add_time'] = time();
    }
    
    public function getProductTypeByPage($where, $field=null, $order='add_time desc',$type){

        if ($field == null) {
            $field = $this->selectFields;
        }
        $count = $this->where($where)->count();
        $page = get_page($count);
        $limit = $page['limit'];
        if($type == 1){
            $limit = 4;
        }
        $data = $this->field($field)->where($where)->limit($limit)->order($order)->select();
        return array(
            'data' => $data,
            'page' => $page['page']
        );
    }

}
