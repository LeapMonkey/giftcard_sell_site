<?php
namespace Sqnbcadmin\Model;
use Think\Model;
use Common\Tools\Emchat;
/**
 * 后台用户管理
 * @author ddf QQ:550308208 liniukeji.com
 *
 */
class NetworkModel extends Model{
	protected $insertFields = array('name','address','phone','tel','level1','level2','level3','add_time','sort','status');
	protected $updateFields = array('name','address','phone','tel','level1','level2','level3','add_time','sort','status');
	protected $selectAllFields = array('id','name','address','phone','tel','level1','level2','level3','add_time','sort','status');
	protected $selectFields = array('id','name','address','phone','tel','level1','level2','level3','add_time','sort','status');
	protected $_validate = array(

		array('name','require','网点名称内容不能为空', self::MUST_VALIDATE),
        array('name', '1,30', '网点名称不能超过30', self::MUST_VALIDATE, 'length', 3),
        array('address','require','具体地址不能为空', self::MUST_VALIDATE),
        // array('telephone','require','酒店电话不能为空', self::MUST_VALIDATE),
         array('level1', array(1,3013), '请选择地区', self::MUST_VALIDATE, 'between', 3),
         array('level2', array(1,3013), '请选择省', self::MUST_VALIDATE, 'between', 3),
         array('level3', array(1,3013), '请选择市', self::MUST_VALIDATE, 'between', 3),

        //array('sort','require','排序不能为空', self::MUST_VALIDATE),
      //  array('url','require','定位链接不能为空', self::MUST_VALIDATE),

	);

    // 获得文章分类详情
    public function search(){
        $name = I('get.name', '','trim');
        $map['status'] = 0;
        if($name) {
            $map['name'] = array('like',"%$name%");
        }

        $data = $this->getArticleByPage($map, $field=null, $order='add_time desc');
       // return $this->_sql();
        return $data;
    }
    protected function _before_insert(&$data,$option) {
        $data['add_time'] = time();
    }

    protected function checkName($data){
        $id = I('get.id', 0, 'intval');
        $where['name'] = $data;
        $where['status']= array('eq',0);
        if ($id > 0) {
            $where['id'] = array('neq', $id );
        }
        $count = $this->where($where)->count();
        if ($count > 0) {
            return false;
        }
        return true;
    }
    /**
     * 获取分页方法
     * @param array $where 传入where条件
     * @param string $order 排序方式
     * @return array 搜索数据和分页数据
     */
    public function getArticleByPage($where, $field=null, $order='sort, id'){
        if ($field == null) {
            $field = $this->selectFields;
        }
        $count = $this->where($where)->count();
        $page = get_page($count);

        $data = $this->field($field)->where($where)->limit($page['limit'])->order($order)->select();

        /*$articleCategoryData = D('ArticleCategory')->field('id,name')->order('sort')->select();

        foreach ($data as $key => $value) {
            foreach ($articleCategoryData as $k => $v) {
                if($value['category_id'] == $v['id']){
                    $data[$key]['category_name'] = $v['name'];
                    break;
                }
            }
        }*/
        return array(
            'data' => $data,
            'page' => $page['page']
        );
    }

}
