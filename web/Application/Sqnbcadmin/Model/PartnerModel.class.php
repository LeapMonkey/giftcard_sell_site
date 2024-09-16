<?php
namespace Sqnbcadmin\Model;
use Think\Model;
/**
 * 合作管理控制器
 * @author zhengnian
 */
class PartnerModel extends Model{
    protected $insertFields = array('title','link','photo_path','add_time');
    protected $updateFields = array('title','link','photo_path','add_time');
    protected $selectFields = array('id','title','link','photo_path','add_time');

    protected $_validate = array(
        array('title','require','标题内容不能为空', self::MUST_VALIDATE),
        array('title', '1,10', '标题名称不能超过5', self::MUST_VALIDATE, 'length', 3),
        array('photo_path','require','图片不能为空', self::MUST_VALIDATE),
    );

    public function getInfo(){
        $title = I('title', '','trim');
        if($title) {
            $map['title'] = array('like',"%$title%");
        }
        $data = $this->getPartnerByPage($map, $field=null, $order='add_time desc');
        return $data;
    }
    protected function _before_insert(&$data,$option) {
        $data['add_time'] = time();
    }

    public function getPartnerByPage($where, $field=null, $order='id'){

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
