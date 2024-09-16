<?php
namespace Sqnbcadmin\Model;
use Think\Model;
/**
 * 产品服务管理控制器
 * @author zhengnian
 */
class ProductModel extends Model{
    protected $insertFields = array('title','define','ideas','feature','advantage','price','used_time','customers','photo_path','add_time','sort','product_type_id','attach_path');
    protected $updateFields = array('title','define','ideas','feature','advantage','price','used_time','customers','photo_path','sort','product_type_id','attach_path');
    protected $selectFields = array('id','title','define','ideas','feature','advantage','price','used_time','customers','photo_path','sort','product_type_id','attach_path');

    protected $_validate = array(
        array('title','require','标题内容不能为空', self::MUST_VALIDATE),
        array('title', '1,15', '标题名称不能超过15', self::MUST_VALIDATE, 'length', 3),

        array('define','require','产品定义不能为空', self::MUST_VALIDATE),
        array('define', '1,40', '产品定义不能超过40', self::MUST_VALIDATE, 'length', 3),

        array('ideas','require','选股思路不能为空', self::MUST_VALIDATE),

        array('feature','require','产品特点不能为空', self::MUST_VALIDATE),
        array('feature', '1,40', '产品特点不能超过40', self::MUST_VALIDATE, 'length', 3),

        array('advantage','require','产品优势不能为空', self::MUST_VALIDATE),
        array('advantage', '1,40', '产品优势不能超过15', self::MUST_VALIDATE, 'length', 3),

        array('price','currency','请输入合法的产品价格',1,'', 3),

        array('used_time', 'number', '非法数据, 使用期限字段', self::MUST_VALIDATE,  3),

        array('customers','require','适合客户不能为空', self::MUST_VALIDATE),
        array('customers', '1,40', '适合客户不能超过40', self::MUST_VALIDATE, 'length', 3),

        array('photo_path','require','图片不能为空', self::MUST_VALIDATE),
        array('photo_path', '1,255', 'PC图片上传有误', self::VALUE_VALIDATE, 'length', 3),

        array('attach_path','require','上传附件不能为空', self::MUST_VALIDATE),

        array('sort','number','排序必须是数字。',3),
    );
    
    public function getInfo($map){
        $title = I('title', '','trim');
        if($title) {
            $map['title'] = array('like',"%$title%");
        }
        $data = $this->getProductByPage($map, $field=null, $order='sort asc');
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
    public function getProductByPage($where, $field=null, $order='sort asc ,add_time desc '){

        if ($field == null) {
            $field = $this->selectFields;
        }
        $count = $this->alias('a')->field('a.*,b.name type_name')
            ->join('ln_product_type  as b on a.product_type_id = b.id ','left')
            ->where($where)
            ->count();
        $page = get_page($count);

        $list = $this->alias('a')->field('a.*,b.name type_name')
            ->join('ln_product_type  as b on a.product_type_id = b.id ','left')
            ->where($where)
            ->limit($page['limit'])->order($order)->select();
        return array(
            'data' => $list,
            'page' => $page['page']
        );
    }

}
