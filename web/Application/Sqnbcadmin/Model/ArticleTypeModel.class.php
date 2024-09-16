<?php
namespace Sqnbcadmin\Model;
use Think\Model;
/**
 * 文章类型管理控制器
 * @author zhengnian
 */
class ArticleTypeModel extends Model{
    protected $insertFields = array('name','add_time','is_member','photo_path','sort');
    protected $updateFields = array('name','is_member','photo_path','sort');
    protected $selectFields = array('id','name','add_time','is_member','photo_path','sort');
    protected $_validate = array(
        array('name','require','分类名称不能为空', self::MUST_VALIDATE),
        array('name', '1,10', '分类名称不能超过10', self::MUST_VALIDATE, 'length', 3),
        array('sort','number','排序必须是数字。',3),
    );
    
    public function getInfo($type){
        $name = I('name', '','trim');
        if($name) {
            $map['name'] = array('like',"%$name%");
        }
        $data = $this->getArticleTypeByPage($map, $field=null, $order='is_member asc, sort asc',$type);
        return $data;
    }

    protected function _before_insert(&$data,$option) {
        $data['add_time'] = time();
    }
    
    public function getArticleTypeByPage($where, $field=null, $order='is_member asc,sort asc',$type){

        if ($field == null) {
            $field = $this->selectFields;
        }
        $userId = UID;
        if($userId == 2){
            $where['id'] = array('in','110,111,115');
        }else if($userId == 3){
            $where['id'] = array('eq','118');
        }else if($userId == 4){
            $where['id'] = array('eq','116');
        }else if($userId == 5){
            $where['id'] = array('eq','117');
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
