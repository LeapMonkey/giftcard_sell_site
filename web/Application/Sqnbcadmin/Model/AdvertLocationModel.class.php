<?php
namespace Sqnbcadmin\Model;
use Think\Model;
/**
 * 广告分类模型
 * @author yuanyulin QQ:755687023
 *
 */
class AdvertLocationModel extends Model{


    protected $_validate = array(
        array('name','require','广告位置不能为空', self::EXISTS_VALIDATE),
        array('name', '1,20', '广告位置不能超过20', self::EXISTS_VALIDATE, 'length', 3),
        array('name', 'checkName', '广告位置已经存在, 不能重复添加', self::EXISTS_VALIDATE, 'callback', 3),
        array('sort', 'number', '排序为数字', self::EXISTS_VALIDATE),
        array('simple_name', '1,20', '广告位置简介不能超过20', self::EXISTS_VALIDATE, 'length', 3),
    );
 
    protected function checkName($data){
        $id = I('id', 0, 'intval');
        $where['name'] = $data;
        if ($id > 0) {
            $where['id'] = array('neq', $id );
        }
        $count = $this->where($where)->count();
        if ($count > 0) {
            return false;
        }
        return true;
    }
    
    // 获得广告详情
    public function search(){        
        $name = I('get.keywords', '','trim');  
        if($name) {
            $map['name'] = array('like',"%$name%");
        }
        $data = $this->getAdvertLocationByPage($map, $field=null);
        return $data;
    }
    
    /**
     * 获取分页方法
     * @param array $where 传入where条件
     * @param string $order 排序方式
     * @return array 搜索数据和分页数据
    */
    public function getAdvertLocationByPage($where, $field=null, $order='sort'){
        if ($field == null) {
            $field = $this->selectFields;
        }
        $count = $this->where($where)->count();
        $page = get_page($count);
        
        $data = $this->field($field)->where($where)->limit($page['limit'])->order($order)->select();
        return array(
            'data' => $data,
            'page' => $page['page']
        );   
    }
}
