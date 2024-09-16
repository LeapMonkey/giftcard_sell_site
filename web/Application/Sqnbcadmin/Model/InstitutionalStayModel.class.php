<?php
namespace Sqnbcadmin\Model;
use Think\Model;
/**
 * 机构入驻列表模型
 * @author yuanyulin QQ:755687023
 *
 */
class InstitutionalStayModel extends Model{
    protected $insertFields = array('treat_time', 'status', 'remark');
    protected $updateFields = array('id', 'treat_time', 'status', 'remark');
    protected $selectFields = array('id', 'name', 'phone', 'institution_name', 'add_time', 'treat_time', 'status', 'treat_time', 'remark');

    protected $_validate = array(
        array('remark', '0,1000', '广告名称不能超过1000个字符', self::VALUE_VALIDATE, 'length', self::MODEL_UPDATE),
    );
    protected function _before_update(&$data, $options) {
        $data['treat_time'] = time();
        $data['status'] = 1;
    }


    // 获得机构入驻详情
    public function search(){        
        $keywords = I('get.keywords', '','trim');  
        if($keywords) {
            $map['name'] = array('like',"%$keywords%");
            $map['phone']  = array('like',"%$keywords%");
            $map['_logic'] = 'or';
        }
        $data = $this->getInstitutionalStayByPage($map, $field=null, 'add_time desc');
        return $data;
    }
    
    /**
     * 获取分页方法
     * @param array $where 传入where条件
     * @param string $order 排序方式
     * @return array 搜索数据和分页数据
    */
    public function getInstitutionalStayByPage($where, $field=null, $order='sort, id'){
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
