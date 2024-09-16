<?php
namespace Sqnbcadmin\Model;
use Think\Model;
/**
 * 帮助控制器
 * @author zhengnian
 */
class HelpModel extends Model{
    protected $insertFields = array('title','content');
    protected $updateFields = array('title','content');
    protected $selectFields = array('id','title','content');

    protected $_validate = array(
        array('title','require','标题内容不能为空', self::MUST_VALIDATE),
        array('title', '1,15', '标题名称不能超过15', self::MUST_VALIDATE, 'length', 3),
        array('content','require','内容不能为空', self::MUST_VALIDATE),
    );
    
    public function getInfo(){
        $title = I('title', '','trim');
        if($title) {
            $map['title'] = array('like',"%$title%");
        }
        $data = $this->getTeamByPage($map, $field=null, $order='add_time desc');
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
    public function getTeamByPage($where, $field=null, $order='id'){

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
