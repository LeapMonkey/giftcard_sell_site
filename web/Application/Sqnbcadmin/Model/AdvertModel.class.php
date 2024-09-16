<?php
namespace Sqnbcadmin\Model;
use Think\Model;
/**
 * 广告分类模型
 * @author yuanyulin QQ:755687023
 *
 */
class AdvertModel extends Model{
    protected $insertFields = array('name','ename','introduce', 'type_id', 'link', 'photo_path', 'start_time', 'end_time', 'display', 'sort');
    protected $updateFields = array('id', 'ename','introduce','name', 'type_id', 'link', 'photo_path', 'start_time', 'end_time', 'display', 'sort');
    protected $selectFields = array('id', 'ename','introduce','name', 'type_id', 'link', 'photo_path', 'start_time', 'end_time', 'display', 'click_count', 'sort');

    protected $_validate = array(
        array('name','require','标题内容不能为空', self::MUST_VALIDATE ),
        array('name', '1,20', '标题名称不能超过20', self::MUST_VALIDATE , 'length', 3),

        array('ename', '1,200', '小标题名称不能超过200', self::VALUE_VALIDATE , 'length', 3),

       
        array('link', '1,200', '链接地址不能超过200', self::VALUE_VALIDATE , 'length', 3),

        array('type_id', '1,1000', '标题类型必须填写', self::MUST_VALIDATE, 'between', 3),
        array('sort','0,1000', '排序必须填写', self::MUST_VALIDATE, 'between', 3),
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
        $name = I('keywords', '','trim');
        if($name) {
            $map['a.name'] = array('like',"%$name%");
        }
        $data = $this->getAdvertByPage($map, $field=null);
        return $data;
    }

    /**
     * 获取分页方法
     * @param array $where 传入where条件
     * @param string $order 排序方式
     * @return array 搜索数据和分页数据
    */
    public function getAdvertByPage($where, $field=null, $order='sort, id'){
        if ($field == null) {
            $field = $this->selectFields;
        }
        $count = $this->alias('a')->where($where)->count();
        $page = get_page($count);

        $data = $this->alias('a')->field('a.*,al.name location_name')->join('__ADVERT_LOCATION__ al on al.id=a.type_id','left')->where($where)->limit($page['limit'])->order($order)->select();
        return array(
            'data' => $data,
            'page' => $page['page']
        );
    }
}
