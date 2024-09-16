<?php
namespace Sqnbcadmin\Model;
use Think\Model;
/**
 * 针股记录管理控制器
 * @author zhengnian
 */
class ReferenceRecordModel extends Model{
    protected $insertFields = array('user_id','phone','number','add_time','is_notice');
    protected $updateFields = array('user_id','phone','number','add_time','is_notice');
    protected $selectFields = array('id','user_id','phone','number','add_time','is_notice');

    protected $_validate = array(
    );
    
    // 获得媒体详情
    public function getInfo(){
        $phone = I('phone','','trim');
        $is_notice = I('is_notice','','trim');
        if($is_notice){
            $map['is_notice'] = array('eq',$is_notice);
        }
        if($phone){
            $map['phone'] = $phone;
        }
        $data = $this->getReferenceRecordByPage($map, $field=null, $order='add_time desc, is_notice desc');
        return $data;
    }
    protected function _before_insert(&$data,$option) {
        $data['add_time'] = time();
    }

    /**
     * 获取分页方法
     * @param array $where 传入where条件
     * @param string $order 排序方式
     * @return array 搜索数据和分页数据
     */
    public function getReferenceRecordByPage($where, $field=null, $order='add_time desc, is_notice desc'){

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
