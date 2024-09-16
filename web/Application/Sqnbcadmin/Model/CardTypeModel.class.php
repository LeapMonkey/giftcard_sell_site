<?php
namespace Sqnbcadmin\Model;
use Think\Model;
/**
 * 卡片管理控制器
 * @author zhengnian
 */
class CardTypeModel extends Model{
    protected $insertFields = array('name','add_time','photo_path','wapphoto_path','wapdropphoto_path');
    protected $updateFields = array('name','add_time','photo_path','wapphoto_path','wapdropphoto_path');
    protected $selectFields = array('id','name','photo_path','add_time','wapphoto_path','wapdropphoto_path');
    protected $_validate = array(
        array('name','require','卡片分类名称不能为空', self::MUST_VALIDATE),
        array('name', '1,10', '卡片分类名称不能超过10', self::MUST_VALIDATE, 'length', 3),
    );
    
    public function getInfo($map){
        $name = I('name', '','trim');
        if($name) {
            $map['name'] = array('like',"%$name%");
        }
        $data = $this->getCardTypeByPage($map, $field=null, $order='id asc');
        return $data;
    }

    protected function _before_insert(&$data,$option) {
        $data['add_time'] = time();
    }
    
    public function getCardTypeByPage($where, $field=null, $order='id asc'){

        if ($field == null) {
            $field = $this->selectFields;
        }
        $count = $this->where($where)->count();
        $page = get_page($count);
        $limit = $page['limit'];

        $data = $this->field($field)->where($where)->limit($limit)->order($order)->select();

        return array(
            'data' => $data,
            'page' => $page['page']
        );
    }

}
